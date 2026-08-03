<?php
namespace ANZ\Appointment\Dto;

class BookingDto extends BaseDto
{
    public function __construct(
        public string $uid,
        public string $clinicUid,
        public string $employeeUid,
        public string $dateTimeBegin,
        public int $serviceDuration
    ){
    }
}
