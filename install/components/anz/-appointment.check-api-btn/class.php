<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.04.2025
 * ==================================================
*/
namespace Chelbit\Umc\Component\Admin;

use Chelbit\Umc\Component\BaseComponent;

class CheckApiBtnComponent extends BaseComponent
{
    function checkRequirements(): bool
    {
        return true;
    }

    function getResult(): array
    {
        return [];
    }
}