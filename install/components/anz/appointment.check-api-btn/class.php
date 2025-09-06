<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.04.2025
 * ==================================================
*/
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