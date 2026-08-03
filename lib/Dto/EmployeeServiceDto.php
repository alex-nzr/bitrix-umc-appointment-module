<?php
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
