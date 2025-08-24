<?php
/*
 * ==================================================
 * This file is part of project bitrix.firstbit.umc
 * 22.08.2025
 * ==================================================
*/
namespace ANZ\Appointment\Dto;

use ANZ\Appointment\Config\TimeSlotStatus;
use DateTime;

class TimeSlotDto extends BaseDto
{
    public function __construct(
        public string $typeOfTimeUid,
        public string $date,
        public string $timeBegin,
        public string $timeEnd,
        public string $formattedDate,
        public string $formattedTimeBegin,
        public string $formattedTimeEnd,
        public DateTime $dateTime,
        public TimeSlotStatus $status
    ){
    }
}