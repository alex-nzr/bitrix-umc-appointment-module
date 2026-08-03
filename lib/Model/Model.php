<?php
namespace ANZ\Appointment\Model;

use Bitrix\Main\ORM\Data\DataManager;
use CBXSanitizer;
use Throwable;

abstract class Model extends DataManager
{
    protected static ?CBXSanitizer $sanitizer = null;

    public static function clearFetchedString(?string $value): string
    {
        try
        {
            if (is_string($value))
            {
                return strip_tags(stripslashes(htmlspecialcharsbx($value)));
            }
        }
        catch(Throwable $e)
        {
            //log error
        }
        return '';
    }

    public static function clearStringBeforeSave(?string $value): string
    {
        try
        {
            if (is_string($value))
            {
                if (is_null(static::$sanitizer))
                {
                    static::$sanitizer = new CBXSanitizer();
                    static::$sanitizer->SetLevel(CBXSanitizer::SECURE_LEVEL_HIGH);
                }

                return static::$sanitizer->SanitizeHtml($value);
            }
        }
        catch(Throwable $e)
        {
            //log error
        }

        return '';
    }
}
