<?php
namespace ANZ\Appointment\Dto;

use DateTime;

class AppointmentDto extends BaseDto
{
    public function __construct(
        public string $uid,
        public string $clinicUid,
        public string $employeeUid,
        public array  $services,
        public int $serviceDuration,
        public DateTime $dateTimeBegin,
        public string $phone,
        public string $lastName,
        public string $name,
        public string $secondName,
        public ?string $email,
        public ?DateTime $birthday,
        public ?string $address,
        public ?string $comment,
    ){
    }
}
