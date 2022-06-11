<?php
namespace FirstBit\Appointment\Event;

use Bitrix\Main\EventManager as BitrixEventManager;
use FirstBit\Appointment\Config\Constants;
use FirstBit\Appointment\Event\Message\Email;
use FirstBit\Appointment\Event\Message\Sms;
use FirstBit\Appointment\Utils\Utils;


class EventManager
{
    protected static array $events = [
        [
            'module'    => 'main',
            'eventType' => 'OnPageStart',
            'class'     => '\\FirstBit\\Appointment\\Event\\Handlers\\Page',
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