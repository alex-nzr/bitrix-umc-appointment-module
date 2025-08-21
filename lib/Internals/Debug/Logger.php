<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 21.11.2022
 * ==================================================
*/
namespace ANZ\Appointment\Internals\Debug;

use ANZ\Appointment\Config\Configuration;
use Bitrix\Main\Diag\Debug;

class Logger extends Debug
{
    public static function print(...$vars): void
    {
        foreach ($vars as $key => $var) {
            echo "$key<pre>";print_r($var);echo "</pre>";
        }
    }

    /**
     * @throws \Exception
     */
    public static function printToFile(...$vars): void
    {
        foreach ($vars as $key => $var)
        {
            static::writeToFile($var, $key, Configuration::getInstance()->getCommonLogFilePath());
        }
    }

    public static function writeToFile($var, $varName = "", $fileName = ""): void
    {
        parent::writeToFile($var, $varName, $fileName);
    }
}