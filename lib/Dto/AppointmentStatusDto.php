<?php
/*
 * ==================================================
 * This file is part of project bitrix.firstbit.umc
 * 23.08.2025
 * ==================================================
*/
namespace ANZ\Appointment\Dto;


class AppointmentStatusDto extends BaseDto
{
    public function __construct(public string $code, public string $name)
    {
    }
}