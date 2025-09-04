<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 23.08.2025
 * ==================================================
*/
namespace ANZ\Appointment\Integration\UmcSdk\Cache;

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

    public function schedule(string $clinicUid = '', array $employees = []): string
    {
        $scope = (!empty($clinicUid) || !empty($employees)) ? $clinicUid . '_' . implode('_', $employees) : 'full';
        return $this->prefix . ".schedule:$scope:$this->siteId";
    }
}