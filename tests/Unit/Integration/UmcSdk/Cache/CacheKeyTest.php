<?php

namespace ANZ\Appointment\Tests\Unit\Integration\UmcSdk\Cache;

use ANZ\Appointment\Integration\UmcSdk\Cache\CacheKey;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CacheKeyTest extends TestCase
{
    public function testScheduleKeyDependsOnDaysClinicEmployeesAndStartDate(): void
    {
        $cacheKey = new CacheKey('s1', 'anz.appointment');

        $firstKey = $cacheKey->schedule(
            14,
            'clinic-1',
            ['employee-1'],
            new DateTimeImmutable('2026-03-26 10:00:00')
        );

        $secondKey = $cacheKey->schedule(
            21,
            'clinic-1',
            ['employee-1'],
            new DateTimeImmutable('2026-03-26 10:00:00')
        );

        $this->assertNotSame($firstKey, $secondKey);
    }

    public function testScheduleKeyNormalizesEmployeeOrder(): void
    {
        $cacheKey = new CacheKey('s1', 'anz.appointment');

        $firstKey = $cacheKey->schedule(14, 'clinic-1', ['employee-2', 'employee-1']);
        $secondKey = $cacheKey->schedule(14, 'clinic-1', ['employee-1', 'employee-2']);

        $this->assertSame($firstKey, $secondKey);
    }
}
