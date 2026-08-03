<?php
namespace ANZ\Appointment\Dto;

abstract class BaseDto
{
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function __toString(): string
    {
        return $this->toJson();
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
