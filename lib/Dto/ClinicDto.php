<?php
namespace ANZ\Appointment\Dto;

class ClinicDto extends BaseDto
{
    public function __construct(
        public string $uid,
        public string $name,
        public array $extra = []
    ){
    }
}
