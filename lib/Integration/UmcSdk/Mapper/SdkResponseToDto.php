<?php
/*
 * ==================================================
 * This file is part of project bitrix.firstbit.umc
 * 22.08.2025
 * ==================================================
*/

namespace ANZ\Appointment\Integration\UmcSdk\Mapper;

use ANZ\Appointment\Config\TimeSlotStatus;
use ANZ\Appointment\Dto\ClinicDto;
use ANZ\Appointment\Dto\EmployeeDto;
use ANZ\Appointment\Dto\ScheduleItemDto;
use ANZ\Appointment\Dto\ServiceDto;
use ANZ\Appointment\Dto\TimeSlotDto;
use DateTime;

class SdkResponseToDto
{
    public function clinicFromArray(array $item): ClinicDto
    {
        return new ClinicDto($item['uid'], $item['name']);
    }

    public function employeeFromArray(array $item): EmployeeDto
    {
        return new EmployeeDto(
            $item['uid'],
            $item['name']
        );
    }

    public function serviceFromArray(array $item): ServiceDto
    {
        return new ServiceDto(
            $item['uid'],
            $item['name']
        );
    }

    public function scheduleItemFromArray(array $item): ScheduleItemDto
    {
        return new ScheduleItemDto(
            $item['uid'],
            $item['name'],
            []
        );
    }

    /**
     * @throws \Exception
     */
    public function timeslotFromArray(array $item): TimeSlotDto
    {
        return new TimeSlotDto(
            new DateTime($item['timeBegin']),
            TimeSlotStatus::FREE
        );
    }
}