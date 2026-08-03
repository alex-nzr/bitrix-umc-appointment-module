<?php
namespace ANZ\Appointment\Dto;


class AppointmentStatusDto extends BaseDto
{
    public function __construct(public string $code, public string $name)
    {
    }
}
