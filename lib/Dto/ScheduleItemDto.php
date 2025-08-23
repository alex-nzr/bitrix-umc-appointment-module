<?php
/*
 * ==================================================
 * This file is part of project bitrix.firstbit.umc
 * 22.08.2025
 * ==================================================
*/
namespace ANZ\Appointment\Dto;

class ScheduleItemDto extends BaseDto
{
    public function __construct(
        public string $clinicUid,
        public string $employeeUid,

        /** @var TimeSlotDto[] $timeslots */
        public array $timeslots,
    ){
    }
}