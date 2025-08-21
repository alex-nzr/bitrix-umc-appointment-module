<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.07.2022
 * ==================================================
*/
namespace ANZ\Appointment\Internals\Installation;

use ANZ\Appointment\Internals\Installation\Event\Email;
use ANZ\Appointment\Internals\Installation\Event\Sms;
use ANZ\Appointment\Tools\Utils;

/**
 * Class EventInstaller
 * @package ANZ\Appointment\Internals\Installation
 */
class EventInstaller
{
    /**
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Exception
     */
    public static function install(): void
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
    public static function uninstall(): void
    {
        $obSms       = new Sms();
        $obEmail     = new Email();

        $obEmail->deleteEmailEvents();
        $obEmail->deleteEmailTemplates();

        $obSms->deleteSmsEvents();
        $obSms->deleteSmsTemplates();
    }
}