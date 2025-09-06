<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 06.09.2025
 * ==================================================
*/

namespace ANZ\Appointment\Tests\Unit\Integration\UmcSdk\Gateway;

use ANZ\Appointment\Dto\AppointmentStatusDto;
use ANZ\Appointment\Dto\ClinicDto;
use ANZ\Appointment\Integration\UmcSdk\Contract\UmcGatewayInterface;
use Throwable;

class FakeSdk implements UmcGatewayInterface
{

    public ?Throwable $toThrow = null;

    public function checkConnection(string $strModeVal, string $url, string $login, string $password, string $token = ''): bool
    {
        if (!is_null($this->toThrow))
        {
            throw $this->toThrow;
        }
        return true;
    }

    public function getClinics(): array
    {
        if (!is_null($this->toThrow))
        {
            throw $this->toThrow;
        }
        return [new ClinicDto('uniq-uid-12345', 'testClinic')];
    }

    /**
     * @inheritDoc
     */
    public function getEmployees(): array
    {
        // TODO: Implement getEmployees() method.
    }

    /**
     * @inheritDoc
     */
    public function getServices(string $clinicUid): array
    {
        // TODO: Implement getServices() method.
    }

    public function getSchedule(int $days = 14, string $clinicUid = '', array $employees = [], ?\DateTime $startDate = null): array
    {
        // TODO: Implement getSchedule() method.
    }

    public function getAppointmentStatus(string $orderUid): AppointmentStatusDto
    {
        // TODO: Implement getAppointmentStatus() method.
    }

    public function bookSlot($reserve): array
    {
        // TODO: Implement bookSlot() method.
    }

    public function addWaitList($waitList): array
    {
        // TODO: Implement addWaitList() method.
    }

    public function sendAppointment($order): array
    {
        // TODO: Implement sendAppointment() method.
    }

    public function deleteAppointment(string $orderUid): array
    {
        // TODO: Implement deleteAppointment() method.
    }
}