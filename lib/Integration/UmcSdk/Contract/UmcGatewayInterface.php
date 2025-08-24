<?php
/*
 * ==================================================
 * This file is part of project bitrix.firstbit.umc
 * 22.08.2025
 * ==================================================
*/
namespace ANZ\Appointment\Integration\UmcSdk\Contract;

use ANZ\Appointment\Dto\AppointmentStatusDto;
use ANZ\Appointment\Dto\ClinicDto;
use ANZ\Appointment\Dto\EmployeeDto;
use ANZ\Appointment\Dto\ScheduleItemDto;
use ANZ\Appointment\Dto\ServiceDto;

interface UmcGatewayInterface
{
    /** @return ClinicDto[] */
    public function getClinics(): array;

    /** @return EmployeeDto[] */
    public function getEmployees(): array;

    /** @return ServiceDto[] */
    public function getServices(string $clinicUid): array;

    public function getSchedule(int $days = 14, string $clinicUid = '', array $employees = [], ?\DateTime $startDate = null): array;

    public function getAppointmentStatus(string $orderUid): AppointmentStatusDto;


    public function bookSlot(/*Order*/ $reserve): array;


    public function addWaitList(/*Order*/ $waitList): array;


    public function sendAppointment(/*Order*/ $order): array;


    public function deleteAppointment(string $orderUid): array;
}