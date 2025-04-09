<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2024
 * ==================================================
 * uclinic.kursk - Configuration.php
 * 03.10.2024 14:26
 * ==================================================
 */


namespace Firstbit\UclinicKursk\Config;

class Configuration
{
    /**
     * @return string
     */
    public static function getOneCLogin(): string
    {
        return Constants::ONE_C_LOGIN;
    }

    /**
     * @return string
     */
    public static function getOneCPassword(): string
    {
        return Constants::ONE_C_PASSWORD;
    }

    /**
     * @return string
     */
    public static function getOneCBaseAddress(): string
    {
        return Constants::ONE_C_ADDRESS;
    }

    /**
     * @return string
     */
    public static function getOneCBaseName(): string
    {
        return Constants::ONE_C_BASE;
    }

    /**
     * @return int
     */
    public static function getDefaultSchedulePeriod(): int
    {
        return Constants::DEFAULT_SCHEDULE_PERIOD_DAYS;
    }

    /**
     * @return int
     */
    public static function getMaxOrdersByDayForClient(): int
    {
        return Constants::MAX_ORDERS_BY_DAY_FOR_CLIENT;
    }

    /**
     * @param string[] $clinicsFromOneC - array of clinic guids from 1c
     * @return string
     */
    public static function getDefaultClinicUid(array $clinicsFromOneC): string
    {
        $defaultClinicUid = Constants::DEFAULT_CLINIC_UID;
        if (!in_array($defaultClinicUid, $clinicsFromOneC))
        {
            $defaultClinicUid = (string)current($clinicsFromOneC);
        }


        return $defaultClinicUid;
    }
}