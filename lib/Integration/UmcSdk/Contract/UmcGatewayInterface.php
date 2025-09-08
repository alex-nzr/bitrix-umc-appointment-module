<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 22.08.2025
 * ==================================================
*/
namespace ANZ\Appointment\Integration\UmcSdk\Contract;

use ANZ\Appointment\Dto\AppointmentStatusDto;
use ANZ\Appointment\Dto\BookingDto;
use ANZ\Appointment\Dto\ClinicDto;
use ANZ\Appointment\Dto\EmployeeDto;
use ANZ\Appointment\Dto\ServiceDto;
use DateTime;

interface UmcGatewayInterface
{
    public function checkConnection(string $strModeVal, string $url, string $login, string $password, string $token = ''): bool;

    /** @return ClinicDto[] */
    public function getClinics(): array;

    /** @return EmployeeDto[] */
    public function getEmployees(): array;

    /** @return ServiceDto[] */
    public function getServices(string $clinicUid): array;

    public function getSchedule(int $days = 14, string $clinicUid = '', array $employees = [], ?DateTime $startDate = null): array;

    public function getAppointmentStatus(string $appointmentUid): AppointmentStatusDto;

    public function bookSlot(string $clinicUid, string $employeeUid, DateTime $dateTimeBegin, int $serviceDuration): BookingDto;

    public function addWaitList(/*Order*/ $waitList): array;

    public function sendAppointment(/*Order*/ $order): array;

    public function deleteAppointment(string $uid): bool;
}