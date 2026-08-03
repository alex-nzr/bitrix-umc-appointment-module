<?php
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
        public TimeSlotStatus $status,
        public array $extra = []
    ){
    }
}
