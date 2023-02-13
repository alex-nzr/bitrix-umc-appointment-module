<?php
namespace Firstbit\Stoma;

use ANZ\Appointment\Soap\XmlParser;
use ANZ\Appointment\Tools\Utils;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

/**
 * Class CustomXmlParser
 * @package Firstbit\Stoma
 */
class CustomXmlParser extends XmlParser
{
    protected static array $departments    = [];
    protected static array $employeesDpt   = [];
    protected static array $schedule       = [];

    /**
     * @param \Bitrix\Main\Event $event
     * @return void
     */
    public static function onBeforeClinicsParsed(Event $event): void
    {
        $data = $event->getParameters();
        if (is_array($data['Клиника']))
        {
            if (Utils::is_assoc($data['Клиника']))
            {
                $data['Клиника'] = [$data['Клиника']];
            }

            foreach ($data['Клиника'] as $clinic) {
                $clinicUid = $clinic['УИД'];
                if (!empty($clinic['бит_СписокПодразделений']['бит_Подразделение']) && is_array($clinic['бит_СписокПодразделений']['бит_Подразделение']))
                {
                    $clinicDepartments = [];
                    foreach ($clinic['бит_СписокПодразделений']['бит_Подразделение'] as $department)
                    {
                        $clinicDepartments[$department['УИД']] = [
                            'uid'       => $department['УИД'],
                            'name'      => $department['Наименование'],
                            'clinicUid' => $clinicUid,
                        ];

                    }
                    static::$departments[$clinicUid] = $clinicDepartments;
                }
            }
        }
    }

    /**
     * @param \Bitrix\Main\Event $event
     * @return \Bitrix\Main\EventResult
     */
    public static function onAfterClinicsParsed(Event $event): EventResult
    {
        $clinics = $event->getParameters();
        foreach (static::$departments as $clinicUid => $clinicDepartments) {
            if (array_key_exists($clinicUid, $clinics)){
                $clinics[$clinicUid]['departments'] = $clinicDepartments;
            }
        }
        return new EventResult(EventResult::SUCCESS, $clinics);
    }

    /**
     * @param \Bitrix\Main\Event $event
     */
    public static function onBeforeEmployeesParsed(Event $event){
        $data = $event->getParameters();
        if (is_array($data['Сотрудник']))
        {
            if (Utils::is_assoc($data['Сотрудник']))
            {
                $data['Сотрудник'] = [$data['Сотрудник']];
            }

            foreach ($data['Сотрудник'] as $employee) {
                $departmentUid = (string)$employee['Подразделение'];
                if ($departmentUid === '00000000-0000-0000-0000-000000000000')
                {
                    $departmentUid = '';
                }
                static::$employeesDpt[$employee['UID']] = $departmentUid;
            }
        }
    }

    /**
     * @param \Bitrix\Main\Event $event
     * @return \Bitrix\Main\EventResult
     */
    public static function onAfterEmployeesParsed(Event $event): EventResult
    {
        $employees = $event->getParameters();
        foreach (static::$employeesDpt as $employeeUid => $departmentUid)
        {
            if (array_key_exists($employeeUid, $employees)){
                $employees[$employeeUid]['departmentUid'] = $departmentUid;
            }
        }
        return new EventResult(EventResult::SUCCESS, $employees);
    }

    /**
     * @param \Bitrix\Main\Event $event
     */
    public static function onBeforeScheduleParsed(Event $event){
        $data = $event->getParameters();
        if (is_array($data['ГрафикДляСайта'])){
            static::$schedule = (new static)->processScheduleData($data['ГрафикДляСайта']);
        }
    }

    /**
     * @param \Bitrix\Main\Event $event
     * @return \Bitrix\Main\EventResult
     */
    public static function onAfterScheduleParsed(Event $event): EventResult
    {
        return new EventResult(EventResult::SUCCESS, static::$schedule);
    }

    /**
     * @param array $schedule
     * @return array[]
     */
    public function processScheduleData(array $schedule): array
    {
        if (Utils::is_assoc($schedule))
        {
            $schedule = [$schedule];
        }

        $employeeUidKey       = $this->fieldMap['SCHEDULE']['SCHEDULE_EMPLOYEE_UID'];
        $employeeFullNameKey  = $this->fieldMap['SCHEDULE']['SCHEDULE_EMPLOYEE_FULL_NAME'];
        $scheduleDurationKey  = $this->fieldMap['SCHEDULE']['SCHEDULE_DURATION'];
        $schedulePeriodsKey   = $this->fieldMap['SCHEDULE']['SCHEDULE_PERIODS'];
        $scheduleOnePeriodKey = $this->fieldMap['SCHEDULE']['SCHEDULE_PERIOD'];
        $scheduleFreeTimeKey  = $this->fieldMap['SCHEDULE']['SCHEDULE_FREE'];
        $scheduleBusyTimeKey  = $this->fieldMap['SCHEDULE']['SCHEDULE_BUSY'];
        $specialtyKey         = $this->fieldMap['EMPLOYEE']['SPECIALTY'];
        $clinicKey            = $this->fieldMap['CLINIC']['CLINIC_KEY'];
        $departmentKey        = 'бит_Подразделение';

        $formattedSchedule = [];
        foreach ($schedule as $key => $item)
        {
            if (isset($item[$employeeUidKey])){
                $formattedSchedule[$key]["refUid"] = $item[$employeeUidKey];
            }
            if (isset($item[$specialtyKey])){
                $formattedSchedule[$key]["specialty"] = $item[$specialtyKey];
            }
            if (isset($item[$employeeFullNameKey])){
                $formattedSchedule[$key]["name"] = $item[$employeeFullNameKey];
            }
            if (isset($item[$clinicKey])){
                $formattedSchedule[$key]["clinicUid"] = $item[$clinicKey];
            }

            $duration = 0;
            if (isset($item[$scheduleDurationKey])){
                $formattedSchedule[$key]["duration"] = $item[$scheduleDurationKey];
                $duration = intval(date("H", strtotime($item[$scheduleDurationKey]))) * 3600
                    + intval(date("i", strtotime($item[$scheduleDurationKey]))) * 60;
                $formattedSchedule[$key]["durationInSeconds"] = $duration;
            }

            $freeTime = (is_array($item[$schedulePeriodsKey][$scheduleFreeTimeKey]) && count($item[$schedulePeriodsKey][$scheduleFreeTimeKey]) > 0)
                ? $item[$schedulePeriodsKey][$scheduleFreeTimeKey][$scheduleOnePeriodKey] : [];
            $busyTime = (is_array($item[$schedulePeriodsKey][$scheduleBusyTimeKey]) && count($item[$schedulePeriodsKey][$scheduleBusyTimeKey]) > 0)
                ? $item[$schedulePeriodsKey][$scheduleBusyTimeKey][$scheduleOnePeriodKey] : [];

            if (Utils::is_assoc($freeTime)) {
                $freeTime = array($freeTime);
            }
            if (Utils::is_assoc($busyTime)) {
                $busyTime = array($busyTime);
            }

            $departmentUid = (string)$item[$departmentKey];
            if ( empty($departmentUid) || ($departmentUid === '00000000-0000-0000-0000-000000000000') )
            {
                $timeItem = current($freeTime);
                if (is_array($timeItem) && !empty($timeItem[$departmentKey]) && ($timeItem[$departmentKey] !== '00000000-0000-0000-0000-000000000000') )
                {
                    $departmentUid = (string)$timeItem[$departmentKey];
                }
                else
                {
                    $departmentUid = '';
                }
            }

            $formattedSchedule[$key]["departmentUid"] = $departmentUid;

            $formattedSchedule[$key]["timetable"]["free"] = $this->formatTimetable($freeTime, $duration);
            $formattedSchedule[$key]["timetable"]["busy"] = $this->formatTimetable($busyTime, 0, true);
            $formattedSchedule[$key]["timetable"]["freeNotFormatted"] = $this->formatTimetable($freeTime, 0, true);
        }
        return [
            "schedule" => $formattedSchedule,
        ];
    }
}