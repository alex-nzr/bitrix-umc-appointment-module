<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.07.2022
 * ==================================================
*/
namespace ANZ\Appointment\Internals\Installation;

use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Result;
use Throwable;

Loc::loadMessages(__FILE__);

/**
 * Class Installer
 * @package ANZ\Appointment\Internals\Installation
 */
class Installer
{
    /**
     * @return \Bitrix\Main\Result
     */
    public static function installModule(): Result
    {
        $result = new Result();
        try
        {
            DBTableInstaller::install();
            EventInstaller::install();
        }
        catch(Throwable $e)
        {
            $result->addError(new Error($e->getMessage()));
        }
        return $result;
    }

    /**
     * @return \Bitrix\Main\Result
     */
    public static function uninstallModule(): Result
    {
        $result = new Result();
        try
        {
            DBTableInstaller::uninstall();
            EventInstaller::uninstall();
        }
        catch(Throwable $e)
        {
            $result->addError(new Error($e->getMessage()));
        }
        return $result;
    }
}