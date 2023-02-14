<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - Installer.php
 * 24.11.2022 19:17
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