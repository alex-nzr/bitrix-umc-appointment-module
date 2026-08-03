<?php
namespace ANZ\Appointment\Dto;

class EmployeeDto extends BaseDto
{
    public function __construct(
        public string $uid,
        public string $name,
        public string $surname,
        public string $middleName,
        public string $fullName,
        public string $clinicUid,
        public string $photo,
        public string $description,
        public string $rating,
        public string $specialtyName,
        public string $specialtyUid,
        /** @var EmployeeServiceDto[] $services */
        public array $services = [],
        public array $extra = [],

    ){
    }
}
