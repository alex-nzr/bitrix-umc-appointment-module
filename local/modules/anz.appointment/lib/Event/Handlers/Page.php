<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - Page.php
 * 10.07.2022 22:37
 * ==================================================
 */
namespace ANZ\Appointment\Event\Handlers;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\UI\Extension;
use CUser;
use Exception;
use ANZ\Appointment\Config\Constants;

/**
 * Class Page
 * @package ANZ\Appointment\Event\Handlers
 */
class Page
{
    private static string $extensionId = Constants::APPOINTMENT_JS_EXTENSION;

    public static function addJsExt()
    {
        global $APPLICATION;
        $currentUserGroups = (new CUser())->GetUserGroupArray();

        if ( !($APPLICATION->GetGroupRight(Constants::APPOINTMENT_MODULE_ID, $currentUserGroups) < "R") )
        {
            if (!Context::getCurrent()->getRequest()->isAdminSection())
            {
                $optionKey = 'appointment_settings_use_auto_injecting';
                if (Option::get(Constants::APPOINTMENT_MODULE_ID, $optionKey) === "Y")
                {
                    try {
                        Extension::load([self::$extensionId]);
                    }catch (Exception $e){}
                }
            }
        }
    }
}