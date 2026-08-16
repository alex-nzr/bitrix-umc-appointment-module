<?php

namespace ANZ\Appointment\Service\Security\RateLimit;

use ANZ\Appointment\Core\Exception\RateLimitExceededException;
use ANZ\Appointment\Model\RateLimitTable;
use ANZ\Appointment\Repository\RateLimit\RateLimitRepository;
use ANZ\Appointment\Repository\RateLimit\RateLimitRepositoryInterface;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Configuration;
use Bitrix\Main\SystemException;
use Closure;
use RuntimeException;

class RateLimiter
{
    private RateLimitRepositoryInterface $repository;
    private Closure $clock;
    private Closure $ipResolver;
    private Closure $secretResolver;

    public function __construct(
        ?RateLimitRepositoryInterface $repository = null,
        ?callable $clock = null,
        ?callable $ipResolver = null,
        ?callable $secretResolver = null
    )
    {
        $this->repository = $repository ?? new RateLimitRepository(RateLimitTable::getEntity());
        $this->clock = $clock !== null ? Closure::fromCallable($clock) : static fn(): int => time();
        $this->ipResolver = $ipResolver !== null ? Closure::fromCallable($ipResolver) : fn(): string => $this->getIp();
        $this->secretResolver = $secretResolver !== null ? Closure::fromCallable($secretResolver) : fn(): string => $this->getCryptoKey();
    }

    /**
     * @throws \Bitrix\Main\SystemException
     */
    public function assertAllowed(string $scope, int $limit, int $ttl, string $identity = '', bool $includeIp = true): void
    {
        $now = ($this->clock)();
        $keyData = $scope . '|' . $identity;
        if ($includeIp)
        {
            $keyData .= '|' . ($this->ipResolver)();
        }

        $key = hash_hmac('sha256', $keyData, ($this->secretResolver)());
        $count = $this->repository->increment($key, $now + $ttl, $now);

        if ($count > $limit)
        {
            throw new RateLimitExceededException($scope, $count === $limit + 1);
        }
    }

    /**
     * @throws \Bitrix\Main\SystemException
     */
    public function isFirstOccurrence(string $scope, int $ttl, string $identity = '', bool $includeIp = false): bool
    {
        $now = ($this->clock)();
        $keyData = $scope . '|' . $identity;
        if ($includeIp)
        {
            $keyData .= '|' . ($this->ipResolver)();
        }

        $key = hash_hmac('sha256', $keyData, ($this->secretResolver)());

        return $this->repository->increment($key, $now + $ttl, $now) === 1;
    }

    private function getIp(): string
    {
        $server = Application::getInstance()->getContext()->getServer();
        return (string)($server->get('REMOTE_ADDR') ?: 'unknown');
    }

    private function getCryptoKey(): string
    {
        $cryptoSettings = Configuration::getValue('crypto');
        $cryptoKey = is_array($cryptoSettings) ? ($cryptoSettings['crypto_key'] ?? '') : '';

        if (!is_string($cryptoKey) || $cryptoKey === '')
        {
            throw new RuntimeException('Bitrix crypto key is not configured.');
        }

        return $cryptoKey;
    }
}
