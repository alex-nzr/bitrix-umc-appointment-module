<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - Debug.php
 * 10.07.2022 22:37
 * ==================================================
 */
namespace FirstBit\Appointment\Tools;

/**
 * Class Debug
 * @package FirstBit\Appointment\Tools
 */
class Debug extends \Bitrix\Main\Diag\Debug
{
    const PATH_TO_LOG_FILE       = __DIR__."/../../log.txt";
    const PATH_TO_LOG_FILE_SHORT = '/local/modules/firstbit.appointment/log.txt';
    /**
     * print vars on screen
     * @param mixed ...$vars
     */
    public static function print(...$vars): void
    {
        foreach ($vars as $var)
        {
            echo "<pre>";
            print_r($var);
            echo "</pre>";
        }
    }

    /**
     * prints vars to file
     * @param mixed ...$vars
     */
    public static function printLog(...$vars): void
    {
        foreach ($vars as $var)
        {
            static::writeToFile($var, '', static::PATH_TO_LOG_FILE_SHORT);
        }
    }
}