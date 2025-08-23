<?php
/*
 * ==================================================
 * This file is part of project bitrix.firstbit.umc
 * 22.08.2025
 * ==================================================
*/
namespace ANZ\Appointment\Tools;

class RequestId
{
    /**
     * @throws \Random\RandomException
     */
    public static function next(): string
    {
        return bin2hex(random_bytes(8)) . '-' . time();
    }
}