<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 08.03.2025
 * ==================================================
*/
namespace ANZ\Appointment\Service;

use Bitrix\Main\Localization\Loc;

class Localization
{
    public static function loadMessages(): void
    {
        Loc::loadMessages(__FILE__);
    }
}