<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 08.03.2025
 * ==================================================
*/
namespace ANZ\Appointment\Service\Cache;

class Validator
{
    const CACHE_TTL = 3600 * 3;
    const SCHEDULE_CACHE_TTL = 5;

    public static function validate(string $filePath, bool $isSchedule = false): bool
    {
        return is_file($filePath)
                && (filemtime($filePath) > (time() - ($isSchedule ? static::SCHEDULE_CACHE_TTL : self::CACHE_TTL)));
    }
}