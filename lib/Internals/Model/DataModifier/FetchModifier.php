<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.07.2022
 * ==================================================
*/
namespace ANZ\Appointment\Internals\Model\DataModifier;

use Throwable;

/**
 * @class FetchModifier
 * @package ANZ\Appointment\Internals\Model\DataModifier
 */
class FetchModifier
{
    /**
     * @param $value
     * @return string
     */
    public static function clearFetchedString($value): string
    {
        try
        {
            if (is_string($value))
            {
                return strip_tags(stripslashes(htmlspecialchars($value)));
            }
        }
        catch(Throwable $e)
        {
            //log error
        }
        return '';
    }
}