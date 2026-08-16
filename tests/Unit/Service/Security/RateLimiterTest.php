<?php

namespace ANZ\Appointment\Tests\Unit\Service\Security;

use ANZ\Appointment\Service\Security\RateLimit\RateLimiter;
use ANZ\Appointment\Core\Exception\RateLimitExceededException;
use ANZ\Appointment\Repository\RateLimit\RateLimitRepositoryInterface;
use Bitrix\Main\SystemException;
use PHPUnit\Framework\TestCase;

class RateLimiterTest extends TestCase
{
    public function testLimitIsSharedBetweenIndependentLimiterInstances(): void
    {
        $storage = new InMemoryRateLimitRepository();
        $firstLimiter = $this->createLimiter($storage);
        $secondLimiter = $this->createLimiter($storage);

        $firstLimiter->assertAllowed('confirm_send', 1, 900, '79990000000');

        $this->expectException(SystemException::class);
        $secondLimiter->assertAllowed('confirm_send', 1, 900, '79990000000');
    }

    public function testExpiredLimitWindowStartsFromFirstRequestAgain(): void
    {
        $storage = new InMemoryRateLimitRepository();
        $now = 1000;
        $limiter = $this->createLimiter($storage, $now);

        $limiter->assertAllowed('confirm_send', 1, 60, '79990000000');

        $now = 1061;
        $limiter->assertAllowed('confirm_send', 1, 60, '79990000000');

        self::assertSame(1, $storage->getCount());
    }

    public function testSharedStorageEnforcesLimitForParallelWorkers(): void
    {
        $storage = new InMemoryRateLimitRepository();
        $firstWorker = $this->createLimiter($storage);
        $secondWorker = $this->createLimiter($storage);

        for ($request = 1; $request <= 10; $request++)
        {
            ($request % 2 === 0 ? $firstWorker : $secondWorker)->assertAllowed('book_slot', 10, 900, 'clinic|employee');
        }

        $this->expectException(SystemException::class);
        $firstWorker->assertAllowed('book_slot', 10, 900, 'clinic|employee');
    }

    public function testGlobalLimitDoesNotDependOnIp(): void
    {
        $storage = new InMemoryRateLimitRepository();
        $now = 1000;
        $firstLimiter = new RateLimiter($storage, static fn(): int => $now, static fn(): string => '127.0.0.1', static fn(): string => 'secret');
        $secondLimiter = new RateLimiter($storage, static fn(): int => $now, static fn(): string => '127.0.0.2', static fn(): string => 'secret');

        $firstLimiter->assertAllowed('confirm_send_global', 1, 900, '', false);

        $this->expectException(SystemException::class);
        $secondLimiter->assertAllowed('confirm_send_global', 1, 900, '', false);
    }

    public function testOnlyFirstRateLimitRejectionIsMarkedForLogging(): void
    {
        $storage = new InMemoryRateLimitRepository();
        $limiter = $this->createLimiter($storage);

        $limiter->assertAllowed('book_slot', 1, 900);

        try
        {
            $limiter->assertAllowed('book_slot', 1, 900);
            self::fail('The second request must exceed the limit.');
        }
        catch (RateLimitExceededException $exception)
        {
            self::assertTrue($exception->isFirstExceeded());
        }

        try
        {
            $limiter->assertAllowed('book_slot', 1, 900);
            self::fail('The third request must exceed the limit.');
        }
        catch (RateLimitExceededException $exception)
        {
            self::assertFalse($exception->isFirstExceeded());
        }
    }

    public function testFirstOccurrenceIsReportedOnlyOnceDuringTtl(): void
    {
        $storage = new InMemoryRateLimitRepository();
        $limiter = $this->createLimiter($storage);

        self::assertTrue($limiter->isFirstOccurrence('public_error_log', 300, 'RuntimeException|0'));
        self::assertFalse($limiter->isFirstOccurrence('public_error_log', 300, 'RuntimeException|0'));
    }

    private function createLimiter(InMemoryRateLimitRepository $storage, int &$now = 1000): RateLimiter
    {
        return new RateLimiter(
            $storage,
            static function () use (&$now): int {
                return $now;
            },
            static fn(): string => '127.0.0.1',
            static fn(): string => 'test-installation-secret'
        );
    }
}

class InMemoryRateLimitRepository implements RateLimitRepositoryInterface
{
    private array $records = [];

    public function increment(string $key, int $expiresAt, int $now): int
    {
        $record = $this->records[$key] ?? [
            'count' => 0,
            'expiresAt' => $expiresAt,
        ];

        if ($record['expiresAt'] <= $now)
        {
            $record = [
                'count' => 0,
                'expiresAt' => $expiresAt,
            ];
        }

        $record['count']++;
        $this->records[$key] = $record;

        return $record['count'];
    }

    public function getCount(): int
    {
        return (int)array_values($this->records)[0]['count'];
    }

    public function deleteExpired(int $now): int
    {
        return 0;
    }
}
