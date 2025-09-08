<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 22.08.2025
 * ==================================================
*/
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