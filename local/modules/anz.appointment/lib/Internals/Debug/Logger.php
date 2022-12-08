<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: alsnazarov@1cbit.ru
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - Logger.php
 * 21.11.2022 12:26
 * ==================================================
 */

namespace ANZ\Appointment\Internals\Debug;

use ANZ\Appointment\Config\Constants;
use Bitrix\Main\Application;
use Bitrix\Main\Diag\Debug;

/**
 * Class Logger
 * @package ANZ\Appointment\Internals\Debug
 */
class Logger extends Debug
{
    /**
     * @param ...$vars
     */
    public static function print(...$vars){
        foreach ($vars as $key => $var) {
            echo "$key)---<pre>";print_r($var);echo "</pre>";
        }
    }

    /**
     * @param ...$vars
     */
    public static function printToFile(...$vars)
    {
        $fileName = str_replace(
            Application::getDocumentRoot(),
            '',
            str_replace(DIRECTORY_SEPARATOR, '/', Constants::PATH_TO_LOGFILE)
        );

        foreach ($vars as $key => $var) {
            static::writeToFile($var, $key.')---', $fileName);
        }
    }

    /**
     * @param $var
     * @param string $varName
     * @param string $fileName
     */
    public static function writeToFile($var, $varName = "", $fileName = "")
    {
        parent::writeToFile($var, $varName, $fileName);
    }
}