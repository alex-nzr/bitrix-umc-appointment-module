<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.07.2022
 * ==================================================
*/
namespace ANZ\Appointment\Core\Installation;

use ANZ\Appointment\Core\Installation\Event\Email;
use ANZ\Appointment\Core\Installation\Event\Sms;
use ANZ\Appointment\Tools\Utils;

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