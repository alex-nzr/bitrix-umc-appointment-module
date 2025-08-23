<?php
/*
 * ==================================================
 * This file is part of project bitrix.firstbit.umc
 * 22.08.2025
 * ==================================================
*/
namespace ANZ\Appointment\Dto;

class ServiceDto extends BaseDto
{
    public function __construct(
        public string $uid,
        public string $name
    ){
    }
}