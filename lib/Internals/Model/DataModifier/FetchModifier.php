<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * mc-portal - FetchModifier.php
 * 16.02.2023 21:33
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