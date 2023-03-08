<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - Utils.php
 * 10.07.2022 22:37
 * ==================================================
 */
namespace ANZ\Appointment\Tools;

use Bitrix\Main\SiteTable;
use DateTime;
use ANZ\Appointment\Config\Constants;

/**
 * Class Utils
 * @package ANZ\Appointment\Tools
 */
class Utils
{
    private function __construct(){}

    /**
     * phone number formatting
     * @param string $phone
     * @return string
     */
    public static function formatPhone(string $phone): string
    {
        $phone = preg_replace(
            '/[^0-9]/',
            '',
            $phone);

        if(strlen($phone) > 10)
        {
            $phone = substr($phone, -10);
            return  '+7' . $phone;
        }
        else
        {
            return  $phone;
        }
    }

    /**
     * @param int|null $interval
     * @return string[]
     */
    public static function getDateInterval(?int $interval = null): array
    {
        if (!is_int($interval)){
            $interval = Constants::DEFAULT_SCHEDULE_PERIOD_DAYS;
        }
        $start  = self::formatDateToISO(strtotime('today + 1 days'));
        $end    = self::formatDateToISO(strtotime('today + ' . $interval . ' days'));
        return [
            "StartDate" => $start,
            "FinishDate" => $end,
        ];
    }

    /**
     * formatting date for 1c
     * @param int $timestamp
     * @return string
     */
    public static function formatDateToISO(int $timestamp): string
    {
        return (new DateTime())->setTimestamp($timestamp)->format('Y-m-d\TH:i:s');
    }

    /**
     * @param string $isoTime
     * @return int
     */
    public static function formatDurationToSeconds(string $isoTime): int
    {
        $minutes = date("i", strtotime($isoTime));
        $hours = date("H", strtotime($isoTime));
        return (int)$minutes*60 + (int)$hours*3600;
    }

    /**
     * @param array $array
     * @return bool
     */
    public static function is_assoc(array $array): bool
    {
        // Keys of the array
        $keys = array_keys($array);
        // If the array keys of the keys match the keys, then the array must
        // not be associative (e.g. the keys array looked like {0:0, 1:1...}).
        return array_keys($keys) !== $keys;
    }

    /**
     * @param string $timeBegin
     * @param string $timeEnd
     * @return string
     * @throws \Exception
     */
    public static function calculateDurationFromInterval(string $timeBegin, string $timeEnd): string
    {
        $startDate = new DateTime($timeBegin);
        $diff = $startDate->diff(new DateTime($timeEnd));

        $hours   = ($diff->h > 9) ? $diff->h : "0".$diff->h;
        $minutes = ($diff->i > 9) ? $diff->i : "0".$diff->i;

        return "0001-01-01T".$hours.":".$minutes.":00";
    }

    /**
     * @param int $seconds
     * @return string
     */
    public static function calculateDurationFromSeconds(int $seconds): string
    {
        $hours = ($seconds >= 3600) ? round($seconds / 3600) : 0;
        $minutes = round(($seconds % 3600) / 60);

        $hours   = ($hours > 9) ? $hours : "0".$hours;
        $minutes = ($minutes > 9) ? $minutes : "0".$minutes;

        return "0001-01-01T".$hours.":".$minutes.":00";
    }

    /**
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function getAllSiteIds(): array
    {
        $siteIds = [];
        $sites = SiteTable::query()->setSelect(['LID'])->exec()->fetchAll();
        if (is_array($sites) && count($sites) > 0){
            foreach ($sites as $site) {
                $siteIds[] = $site['LID'];
            }
        }
        return $siteIds;
    }
}