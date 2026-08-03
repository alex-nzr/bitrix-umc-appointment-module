<?php
namespace ANZ\Appointment\Core\Installation;

use ANZ\Appointment\Config\Configuration;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Result;
use Throwable;

class Installer
{
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

    public static function uninstallModule(): Result
    {
        $result = new Result();
        try
        {
            DBTableInstaller::uninstall();
            EventInstaller::uninstall();
            Option::delete(Configuration::getModuleId());
        }
        catch(Throwable $e)
        {
            $result->addError(new Error($e->getMessage()));
        }
        return $result;
    }
}
