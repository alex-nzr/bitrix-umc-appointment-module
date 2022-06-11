<?php
namespace FirstBit\Appointment\Event\Handlers;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\UI\Extension;
use Exception;
use FirstBit\Appointment\Config\Constants;

class Page
{
    private static string $extensionId = Constants::APPOINTMENT_JS_EXTENSION;

    public static function addJsExt()
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