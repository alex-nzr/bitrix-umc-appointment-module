<?php
namespace ANZ\Appointment\Component;

class CheckApiBtnComponent extends BaseComponent
{
    public function onPrepareComponentParams($arParams): array
    {
        return array_merge($arParams, [
            "CACHE_TYPE" => "N",
            "CACHE_TIME" => 0,
        ]);
    }

    function checkRequirements(): bool
    {
        return true;
    }

    function getResult(): array
    {
        return [];
    }
}
