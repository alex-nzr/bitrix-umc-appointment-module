<?php
namespace FirstBit\Appointment\Utils;

use Bitrix\Main\Config\Option;
use Bitrix\Main\SiteTable;
use DateTime;
use Exception;
use FirstBit\Appointment\Config\Constants;

class Utils{
    private function __construct(){}

    /** phone number formatting
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

    /** creates array of date interval
     * @param int $interval
     * @return array
     */
    public static function getDateInterval(int $interval): array
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

    /** formatting date for 1c
     * @param int $timestamp
     * @return string
     */
    public static function formatDateToISO(int $timestamp): string
    {
        return (new DateTime())->setTimestamp($timestamp)->format('Y-m-d\TH:i:s');
    }

    public static function formatDurationToSeconds(string $isoTime): int
    {
        $minutes = date("i", strtotime($isoTime));
        $hours = date("H", strtotime($isoTime));
        return (int)$minutes*60 + (int)$hours*3600;
    }

    /**
     * @param string $message
     * @return string[]
     */
    public static function createErrorArray(string $message): array
    {
        return ["error" => $message];
    }

    /** print vars on screen
     * @param mixed ...$vars
     */
    public static function print(...$vars): void
    {
        foreach ($vars as $var) {
            echo "<pre>";
            print_r($var);
            echo "</pre>";
        }
    }

    /** prints param to file
     * @param $data
     */
    public static function printLog($data): void
    {
        $log = print_r($data, true);
        file_put_contents(
            Constants::PATH_TO_LOG_FILE,
            $log . PHP_EOL,
            FILE_APPEND
        );
    }

    /** Tests if an array is associative or not.
     * @param array array to check
     * @return boolean
     */
    public static function is_assoc(array $array): bool
    {
        if (!is_array($array)){
            return false;
        }

        // Keys of the array
        $keys = array_keys($array);
        // If the array keys of the keys match the keys, then the array must
        // not be associative (e.g. the keys array looked like {0:0, 1:1...}).
        return array_keys($keys) !== $keys;
    }

    /** prepare schedule data for frontend
     * @param array $schedule
     * @return array
     */
    public static function prepareScheduleData(array $schedule): array
    {
        try
        {
            if (self::is_assoc($schedule))
            {
                $schedule = array($schedule);
            }

            $formattedSchedule = [];
            foreach ($schedule as $key => $item)
            {
                if (isset($item["СотрудникID"])){
                    $formattedSchedule[$key]["refUid"] = $item["СотрудникID"];
                }
                if (isset($item["Специализация"])){
                    $formattedSchedule[$key]["specialty"] = $item["Специализация"];
                }
                if (isset($item["СотрудникФИО"])){
                    $formattedSchedule[$key]["name"] = $item["СотрудникФИО"];
                }
                if (isset($item["Клиника"])){
                    $formattedSchedule[$key]["clinicUid"] = $item["Клиника"];
                }

                $duration = 0;
                if (isset($item["ДлительностьПриема"])){
                    $formattedSchedule[$key]["duration"] = $item["ДлительностьПриема"];
                    $duration = intval(date("H", strtotime($item["ДлительностьПриема"]))) * 3600
                        + intval(date("i", strtotime($item["ДлительностьПриема"]))) * 60;
                    $formattedSchedule[$key]["durationInSeconds"] = $duration;
                }

                $freeTime = (is_array($item["ПериодыГрафика"]["СвободноеВремя"]) && count($item["ПериодыГрафика"]["СвободноеВремя"]) > 0)
                    ? $item["ПериодыГрафика"]["СвободноеВремя"]["ПериодГрафика"] : [];
                $busyTime = (is_array($item["ПериодыГрафика"]["ЗанятоеВремя"]) && count($item["ПериодыГрафика"]["ЗанятоеВремя"]) > 0)
                    ? $item["ПериодыГрафика"]["ЗанятоеВремя"]["ПериодГрафика"] : [];

                if (self::is_assoc($freeTime)) {
                    $freeTime = array($freeTime);
                }
                if (self::is_assoc($busyTime)) {
                    $busyTime = array($busyTime);
                }

                $formattedSchedule[$key]["timetable"]["free"] = self::formatTimetable($freeTime, $duration);
                $formattedSchedule[$key]["timetable"]["busy"] = self::formatTimetable($busyTime, 0, true);
                $formattedSchedule[$key]["timetable"]["freeNotFormatted"] = self::formatTimetable($freeTime, 0, true);
            }
            return [
                "schedule" => $formattedSchedule,
            ];
        }
        catch (Exception $e){
            return self::createErrorArray($e->getMessage());
        }
    }

    /** Beautify array of timelines
     * @param $array
     * @param int $duration
     * @param bool $useDefaultInterval
     * @return array
     */
    public static function formatTimetable($array, int $duration, $useDefaultInterval = false): array
    {
        if (!is_array($array) || empty($array)){
            return [];
        }

        if (!$duration > 0){
            $duration = Option::get(
                Constants::APPOINTMENT_MODULE_ID,
                'appointment_settings_default_duration',
                Constants::DEFAULT_APPOINTMENT_DURATION_SEC
            );
        }

        if (!empty($array)){
            if (self::is_assoc($array)) {
                $array = array($array);
            }
            $formattedArray = [];
            foreach ($array as $item)
            {
                $timestampTimeBegin = strtotime($item["ВремяНачала"]);
                $timestampTimeEnd = strtotime($item["ВремяОкончания"]);

                if ($useDefaultInterval){
                    $formattedArray[] = [
                        "typeOfTimeUid" => $item["ВидВремени"],
                        "date" => $item["Дата"],
                        "timeBegin" => date("Y-m-d", $timestampTimeBegin) ."T". date("H:i:s", $timestampTimeBegin),
                        "timeEnd" => date("Y-m-d", $timestampTimeEnd) ."T". date("H:i:s", $timestampTimeEnd),
                        "formattedDate" => date("d-m-Y", strtotime($item["Дата"])),
                        "formattedTimeBegin" => date("H:i", $timestampTimeBegin),
                        "formattedTimeEnd" => date("H:i", $timestampTimeEnd),
                    ];
                }
                else
                {
                    $timeDifference = $timestampTimeEnd - $timestampTimeBegin;
                    $appointmentsCount = round($timeDifference / $duration);

                    for ($i = 0; $i < $appointmentsCount; $i++)
                    {
                        $start = $timestampTimeBegin + ($duration * $i);
                        $end = $timestampTimeBegin + ($duration * ($i+1));

                        $formattedArray[] = [
                            "typeOfTimeUid" => $item["ВидВремени"],
                            "date" => $item["Дата"],
                            "timeBegin" => date("Y-m-d", $start) ."T". date("H:i:s", $start),
                            "timeEnd" => date("Y-m-d", $end) ."T". date("H:i:s", $end),
                            "formattedDate" => date("d-m-Y", strtotime($item["Дата"])),
                            "formattedTimeBegin" => date("H:i", $start),
                            "formattedTimeEnd" => date("H:i", $end),
                        ];
                    }
                }
            }
            return $formattedArray;
        }
        else
        {
            return [];
        }
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