<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 22.08.2025
 * ==================================================
*/
namespace ANZ\Appointment\Dto;

class EmployeeServiceDto extends BaseDto
{
    public function __construct(
        public string $uid,
        public int $personalDuration,
        public array $extra = []
    ){
    }
}
