<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - EventManager.php
 * 10.07.2022 22:37
 * ==================================================
 */
namespace ANZ\Appointment\Event;

use Bitrix\Main\EventManager as BitrixEventManager;
use ANZ\Appointment\Config\Constants;
use ANZ\Appointment\Event\Message\Email;
use ANZ\Appointment\Event\Message\Sms;
use ANZ\Appointment\Tools\Utils;

/**
 * Class EventManager
 * @package ANZ\Appointment\Event
 */
class EventManager
{
    protected static array $events = [
        [
            'module'    => 'main',
            'eventType' => 'OnPageStart',
            'class'     => '\\ANZ\\Appointment\\Event\\Handlers\\Page',
            'method'    => 'addJsExt',
            'sort'      => 100
        ],
    ];

    public static function addEventHandlers()
    {
        foreach (static::$events as $event)
        {
            BitrixEventManager::getInstance()->registerEventHandlerCompatible(
                $event['module'],
                $event['eventType'],
                Constants::APPOINTMENT_MODULE_ID,
                $event['class'],
                $event['method'],
                $event['sort'] ?? 100,
            );
        }
    }

    public static function removeEventHandlers()
    {
        foreach (static::$events as $event)
        {
            BitrixEventManager::getInstance()->unRegisterEventHandler(
                $event['module'],
                $event['eventType'],
                Constants::APPOINTMENT_MODULE_ID,
                $event['class'],
                $event['method'],
            );
        }
    }

    /**
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Exception
     */
    public static function createMessageEvents(): void
    {
        $siteIds = Utils::getAllSiteIds();
        $obSms   = new Sms();
        $obEmail = new Email();

        $obEmail->createEmailNoteEvent();
        $obEmail->createEmailNoteTemplate($siteIds);

        $obEmail->createEmailConfirmEvent();
        $obEmail->createEmailConfirmTemplate($siteIds);

        $obSms->createSmsConfirmEvent();
        $obSms->createSmsConfirmTemplate($siteIds);
    }

    /**
     * @throws \Exception
     */
    public static function deleteMessageEvents(): void
    {
        $obSms       = new Sms();
        $obEmail     = new Email();

        $obEmail->deleteEmailEvents();
        $obEmail->deleteEmailTemplates();

        $obSms->deleteSmsEvents();
        $obSms->deleteSmsTemplates();
    }
}