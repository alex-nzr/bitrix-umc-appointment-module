<?php
namespace Firstbit\Stoma;

use ANZ\Appointment\Service\Provider\ExchangeDataProvider;
use ANZ\BitUmc\SDK\Core\Config\Parameters;
use ANZ\BitUmc\SDK\Core\Dictionary\SoapResponseKey;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use ReflectionClass;

/**
 * Class CustomXmlParser
 * @package Firstbit\Stoma
 */
class CustomXmlParser extends ExchangeDataProvider
{
    protected static array $departments    = [];
    protected static array $employeesDpt   = [];
    protected static array $schedule       = [];

    public static function onBeforeClinicsParsed(Event $event): void
    {
        $data = $event->getParameters();
        if (is_array($data['Клиника']))
        {
            if (!array_is_list($data['Клиника']))
            {
                $data['Клиника'] = [$data['Клиника']];
            }

            foreach ($data['Клиника'] as $clinic)
            {
                if (!is_array($clinic))
                {
                    continue;
                }
                $clinicUid = $clinic['УИД'];
                if (is_array($clinic['бит_СписокПодразделений']) && is_array($clinic['бит_СписокПодразделений']['бит_Подразделение']))
                {
                    foreach ($clinic['бит_СписокПодразделений']['бит_Подразделение'] as $department)
                    {
                        if (!is_array(static::$departments[$clinicUid]))
                        {
                            static::$departments[$clinicUid] = [];
                        }
                        static::$departments[$clinicUid][$department['УИД']] = [
                            'uid'       => $department['УИД'],
                            'name'      => $department['Наименование'],
                            'clinicUid' => $clinicUid,
                        ];

                    }
                }
            }
        }
    }

    public static function onAfterClinicsParsed(Event $event): EventResult
    {
        $clinics = $event->getParameters();
        foreach (static::$departments as $clinicUid => $clinicDepartments)
        {
            if (array_key_exists($clinicUid, $clinics))
            {
                $clinics[$clinicUid]['departments'] = $clinicDepartments;
            }
        }
        return new EventResult(EventResult::SUCCESS, $clinics);
    }

    public static function onBeforeEmployeesParsed(Event $event): void
    {
        $data = $event->getParameters();
        if (is_array($data['Сотрудник']))
        {
            if (!array_is_list($data['Сотрудник']))
            {
                $data['Сотрудник'] = [$data['Сотрудник']];
            }

            foreach ($data['Сотрудник'] as $employee)
            {
                $departmentUid = (string)$employee['Подразделение'];
                if ($departmentUid === '00000000-0000-0000-0000-000000000000')
                {
                    $departmentUid = '';
                }
                static::$employeesDpt[$employee['UID']] = $departmentUid;
            }
        }
    }

    public static function onAfterEmployeesParsed(Event $event): EventResult
    {
        $employees = $event->getParameters();
        foreach (static::$employeesDpt as $employeeUid => $departmentUid)
        {
            if (array_key_exists($employeeUid, $employees))
            {
                $employees[$employeeUid]['departmentUid'] = $departmentUid;
            }
        }
        return new EventResult(EventResult::SUCCESS, $employees);
    }

    /**
     * @throws \ReflectionException
     */
    public static function onBeforeScheduleParsed(Event $event): void
    {
        $data = $event->getParameters();
        if (is_array($data['ГрафикДляСайта']))
        {
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
     * @throws \ReflectionException
     */
    public function processScheduleData(array $schedule): array
    {
        if (!array_is_list($schedule))
        {
            $schedule = [$schedule];
        }

        $employeeUidKey       = SoapResponseKey::EMPLOYEE_UID->value;
        $employeeFullNameKey  = SoapResponseKey::EMPLOYEE_FULL_NAME->value;
        $scheduleDurationKey  = SoapResponseKey::SCHEDULE_DURATION->value;
        $schedulePeriodsKey   = SoapResponseKey::SCHEDULE_PERIODS->value;
        $scheduleOnePeriodKey = SoapResponseKey::SCHEDULE_PERIOD->value;
        $scheduleFreeTimeKey  = SoapResponseKey::SCHEDULE_FREE_TIME->value;
        $scheduleBusyTimeKey  = SoapResponseKey::SCHEDULE_BUSY_TIME->value;
        $specialtyKey         = SoapResponseKey::SPECIALTY->value;
        $clinicKey            = SoapResponseKey::CLINIC->value;
        $departmentKey        = 'бит_Подразделение';

        $sdkParser = (new static)->sdkXmlParser;
        $reflectionClass = new ReflectionClass(get_class($sdkParser));
        $reflectionSpecUidMethod = $reflectionClass->getMethod('getSpecialtyUid');
        $reflectionSpecUidMethod->setAccessible(true);

        $formattedSchedule = [];
        foreach ($schedule as $item)
        {
            if (!empty($item[$clinicKey]))
            {
                $clinicUid = $item[$clinicKey];
                if (!key_exists($clinicUid, $formattedSchedule) || !is_array($formattedSchedule[$clinicUid]))
                {
                    $formattedSchedule[$clinicUid] = [];
                }

                $departmentUid = $item[$departmentKey];
                if (empty($departmentUid) || ($departmentUid === '00000000-0000-0000-0000-000000000000'))
                {
                    $freeTime = (is_array($item[$schedulePeriodsKey][$scheduleFreeTimeKey]) && count($item[$schedulePeriodsKey][$scheduleFreeTimeKey]) > 0)
                        ? $item[$schedulePeriodsKey][$scheduleFreeTimeKey][$scheduleOnePeriodKey] : [];
                    $timeItem = current($freeTime);
                    if (is_array($timeItem) && isset($timeItem[$departmentKey]) && ($timeItem[$departmentKey] !== '00000000-0000-0000-0000-000000000000') )
                    {
                        $departmentUid = (string)$timeItem[$departmentKey];
                    }
                }

                if (!key_exists($departmentUid, $formattedSchedule[$clinicUid])
                    || !is_array($formattedSchedule[$clinicUid][$departmentUid])
                )
                {
                    $formattedSchedule[$clinicUid][$departmentUid] = [];
                }

                $specialtyName = !empty($item[$specialtyKey]) ? $item[$specialtyKey] : SoapResponseKey::EMPTY_SPECIALTY->value;
                $specialtyUid  = $reflectionSpecUidMethod->invoke($sdkParser, $specialtyName);

                if (!key_exists($specialtyUid, $formattedSchedule[$clinicUid][$departmentUid])
                    || !is_array($formattedSchedule[$clinicUid][$departmentUid][$specialtyUid])
                ){
                    $formattedSchedule[$clinicUid][$departmentUid][$specialtyUid] = [];
                }

                if (!empty($item[$employeeUidKey]))
                {
                    $employeeUid     = $item[$employeeUidKey];
                    $employeeName    = $item[$employeeFullNameKey];

                    $durationSeconds = Parameters::DEFAULT_DURATION;
                    $durationFrom1C  = '';
                    if (!empty($item[$scheduleDurationKey]))
                    {
                        $durationFrom1C  = $item[$scheduleDurationKey];
                        $durationSeconds = intval(date("H", strtotime($durationFrom1C))) * 3600
                            + intval(date("i", strtotime($durationFrom1C))) * 60;
                    }

                    if (empty($formattedSchedule[$clinicUid][$departmentUid][$specialtyUid][$employeeUid]))
                    {
                        $formattedSchedule[$clinicUid][$departmentUid][$specialtyUid][$employeeUid] = [
                            'specialtyName'     => $specialtyName,
                            'employeeName'      => $employeeName,
                            'durationFrom1C'    => $durationFrom1C,
                            'durationInSeconds' => $durationSeconds,
                            'timetable'         => [
                                'freeFormatted' => [],
                                'busy'          => [],
                                'free'          => [],
                            ]
                        ];
                    }

                    $timetable = [];

                    $freeTime = (is_array($item[$schedulePeriodsKey][$scheduleFreeTimeKey]) && count($item[$schedulePeriodsKey][$scheduleFreeTimeKey]) > 0)
                        ? $item[$schedulePeriodsKey][$scheduleFreeTimeKey][$scheduleOnePeriodKey] : [];
                    if (!array_is_list($freeTime))
                    {
                        $freeTime = [$freeTime];
                    }

                    $busyTime = (is_array($item[$schedulePeriodsKey][$scheduleBusyTimeKey]) && count($item[$schedulePeriodsKey][$scheduleBusyTimeKey]) > 0)
                        ? $item[$schedulePeriodsKey][$scheduleBusyTimeKey][$scheduleOnePeriodKey] : [];
                    if (!array_is_list($busyTime))
                    {
                        $busyTime = [$busyTime];
                    }

                    $timetable['free'] = array_merge(
                        $formattedSchedule[$clinicUid][$departmentUid][$specialtyUid][$employeeUid]['timetable']['free'],
                        $sdkParser->formatTimetable($freeTime, 0, true)
                    );
                    $timetable['busy'] = array_merge(
                        $formattedSchedule[$clinicUid][$departmentUid][$specialtyUid][$employeeUid]['timetable']['busy'],
                        $sdkParser->formatTimetable($busyTime, 0, true)
                    );
                    $timetable['freeFormatted'] = array_merge(
                        $formattedSchedule[$clinicUid][$departmentUid][$specialtyUid][$employeeUid]['timetable']['freeFormatted'],
                        $sdkParser->formatTimetable($freeTime, $durationSeconds)
                    );

                    $formattedSchedule[$clinicUid][$departmentUid][$specialtyUid][$employeeUid]['timetable'] = $timetable;
                }
            }
        }

        return $formattedSchedule;
    }
}