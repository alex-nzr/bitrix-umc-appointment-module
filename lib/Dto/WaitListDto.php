<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 22.08.2025
 * ==================================================
*/
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