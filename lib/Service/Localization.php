<?php
namespace ANZ\Appointment\Service;

use Bitrix\Main\Localization\Loc;

class Localization
{
    public static function loadMessages(): void
    {
        Loc::loadMessages(__FILE__);
    }
}
