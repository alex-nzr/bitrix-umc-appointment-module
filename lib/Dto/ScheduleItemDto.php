<?php
namespace ANZ\Appointment\Dto;

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
        public array $extra = [],
    ){
    }
}
