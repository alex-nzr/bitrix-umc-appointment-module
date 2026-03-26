<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 23.08.2025
 * ==================================================
*/
namespace ANZ\Appointment\Integration\UmcSdk\Cache;

use DateTimeInterface;

class CacheKey
{
    public function __construct(protected string $siteId, protected string $prefix = '')
    {
    }

    public function clinics(): string
    {
        return $this->prefix . ".clinics:$this->siteId";
    }

    public function employees(): string
    {
        return $this->prefix . ".employees:$this->siteId";
    }

    public function services(string $clinicUid = ''): string
    {
        return $this->prefix . ".services:$clinicUid:$this->siteId";
    }

    public function schedule(
        int $days = 14,
        string $clinicUid = '',
        array $employees = [],
        ?DateTimeInterface $startDate = null
    ): string
    {
        $employees = array_values(array_unique(array_filter($employees, 'strlen')));
        sort($employees);

        $scope = [
            'days:' . $days,
            'clinic:' . ($clinicUid !== '' ? $clinicUid : 'all'),
            'employees:' . (!empty($employees) ? implode('_', $employees) : 'all'),
            'start:' . ($startDate?->format(DATE_ATOM) ?? 'default'),
        ];

        return $this->prefix . '.schedule:' . md5(implode('|', $scope)) . ':' . $this->siteId;
    }
}
