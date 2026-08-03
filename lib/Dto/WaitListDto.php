<?php
namespace ANZ\Appointment\Dto;

use DateTime;

class WaitListDto extends BaseDto
{
    public function __construct(
        public string $clinicUid,
        public string $clinicName,
        public string $employeeUid,
        public string $employeeName,
        public string $specialtyName,
        public array  $services,
        public DateTime $dateTimeBegin,
        public string $phone,
        public string $lastName,
        public string $name,
        public string $secondName,
        public ?string $email,
        public ?string $comment,
    ){
    }
}
