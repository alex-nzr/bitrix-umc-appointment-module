<?php
/*
 * ==================================================
 * This file is part of project bitrix.firstbit.umc
 * 22.08.2025
 * ==================================================
*/
namespace ANZ\Appointment\Dto;

use ANZ\Appointment\Config\TimeSlotStatus;

class ScheduleItemDto extends BaseDto
{
    public function __construct(
        public string $clinicUid,
        public string $specialtyUid,
        public string $employeeUid,
        public string $specialtyName,
        public string $employeeName,
        public int $durationInSeconds,

        /** @var [TimeSlotStatus => TimeSlotDto[]] */
        public array $timeslots,
    ){
    }
}