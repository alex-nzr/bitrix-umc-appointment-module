<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * mc-portal - SaveModifier.php
 * 16.02.2023 21:33
 * ==================================================
 */
namespace ANZ\Appointment\Internals\Model\DataModifier;

use CBXSanitizer;
use Throwable;

/**
 * @class SaveModifier
 * @package ANZ\Appointment\Internals\Model\DataModifier
 */
class SaveModifier
{
    private static ?CBXSanitizer $sanitizer = null;

    /**
     * @param $value
     * @return string
     */
    public static function clearStringBeforeSave($value): string
    {
        try
        {
            if (is_string($value))
            {
                if (static::$sanitizer === null)
                {
                    static::$sanitizer = new CBXSanitizer;
                    static::$sanitizer->setLevel(CBXSanitizer::SECURE_LEVEL_HIGH);
                }

                return static::$sanitizer->sanitizeHtml($value);
            }
        }
        catch(Throwable $e)
        {
            //log error
        }

        return '';
    }
}