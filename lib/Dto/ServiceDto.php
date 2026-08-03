<?php
namespace ANZ\Appointment\Dto;

class ServiceDto extends BaseDto
{
    public function __construct(
        public string $uid,
        public string $name,
        public string $typeOfItem,
        public string $artNumber,
        public int|float $price,
        public int $duration,
        public string $measureUnit,
        public string $parent,
        public array $extra = [],
    ){
    }
}
