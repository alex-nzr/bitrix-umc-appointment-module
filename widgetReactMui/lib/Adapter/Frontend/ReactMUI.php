<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2024
 * ==================================================
 * uclinic.kursk - ReactMUI.php
 * 03.10.2024 14:14
 * ==================================================
 */
namespace Firstbit\UclinicKursk\Adapter\Frontend;

use ANZ\BitUmc\SDK\Core\Dictionary\SoapResponseKey;
use Firstbit\UclinicKursk\Config\Configuration;

/**
 * Класс адаптирует данные, получаемые от bit-umc-sdk в формат понятный фронтенду на ReactMUI
 * @class ReactMUI
 * @package Firstbit\UclinicKursk\Adapter\Frontend
 */
class ReactMUI
{
    private static array $clinics = [];

    /**
     * @param array $clinicsData
     * @return array
     */
    public static function prepareClinicsData(array $clinicsData = []): array
    {
        static::$clinics = array_values($clinicsData);
        $defaultClinicId = Configuration::getDefaultClinicUid(array_keys($clinicsData));
        foreach (static::$clinics as $key => $clinic)
        {
            static::$clinics[$key]['isDefault'] = ($clinic['uid'] === $defaultClinicId);
        }
        return static::$clinics;
    }

    /**
     * @param array $employees
     * @return array
     */
    public static function prepareEmployeesData(array $employees = []): array
    {
        $preparedData = [];
        $clinicIds = array_column(static::$clinics, 'uid');
        $defaultClinicUid = Configuration::getDefaultClinicUid($clinicIds);
        foreach ($employees as $employee)
        {
            if (!empty($employee['specialtyName']))
            {
                $employee['specialties'] = [];
                $specialties = explode(',', $employee['specialtyName']);
                foreach ($specialties as $specialtyName)
                {
                    $specialtyName = trim($specialtyName);
                    if (!empty($specialtyName))
                    {
                        $specialtyUid = static::getSpecialtyUid($specialtyName);
                        $employee['specialties'][$specialtyUid] = [
                            'name' => $specialtyName
                        ];
                    }
                }

                unset(
                    $employee['specialtyName'],
                    $employee['specialtyUid'],
                    $employee['photo'],
                    $employee['description'],
                    $employee['rating'],
                );
            }

            if (empty($employee['clinicUid']))
            {
                $employee['clinicUid'] = $defaultClinicUid;
            }

            $employee['clinic'] = "";
            foreach (static::$clinics as $clinic)
            {
                if ($clinic['uid'] === $employee['clinicUid'])
                {
                    $employee['clinic'] = $clinic['name'];
                }
            }

            /*if (is_array($employee['services']))
            {
                foreach ($employee['services'] as $serviceUid => $serviceData)
                {
                    $employee['services'][$serviceUid] = [
                        'title' => ''
                    ];
                }
            }*/

            $preparedData[$employee['uid']] = $employee;
        }
        unset($employees);
        return $preparedData;
    }

    /**
     * @param array $nomenclatureData
     * @param string $clinicUid
     * @return array
     */
    public static function prepareNomenclatureData(array $nomenclatureData = [], string $clinicUid = ''): array
    {
        $preparedData = [];
        foreach ($nomenclatureData as $uid => $nomenclature)
        {
            $nomenclature['prices'] = [];
            $nomenclature['specialty'] = (string)$nomenclature['parent'];
            $nomenclature['specialtyUid'] = !empty($nomenclature['parent'])
                ? static::getSpecialtyUid($nomenclature['parent'])
                : SoapResponseKey::EMPTY_SPECIALTY->value;
            unset($nomenclature['parent']);

            $nomenclature['prices'][$clinicUid] = [
                "price" => $nomenclature['price']
            ];

            $preparedData[$uid] = $nomenclature;
        }
        unset($nomenclatureData);
        return $preparedData;
    }

    /**
     * @param array $schedule
     * @return array
     */
    public static function prepareScheduleData(array $schedule = []): array
    {
        $preparedData = [];
        foreach ($schedule as $clinicUid => $clinicSchedule)
        {
            foreach ($clinicSchedule as $specialtyUid => $clinicSpecialtySchedule)
            {
                foreach ($clinicSpecialtySchedule as $employeeUid => $employeeSchedule)
                {
                    $preparedEmployeeData = [
                        'refUid' => $employeeUid,
                        'specialty' => $employeeSchedule['specialtyName'],
                        'specialtyUid' => $specialtyUid,
                        'name' => $employeeSchedule['employeeName'],
                        'clinicUid' => $clinicUid,
                        'duration' => $employeeSchedule['durationFrom1C'],
                        'durationInSeconds' => $employeeSchedule['durationInSeconds'],
                        'timetable' => [
                            'free' => [],
                            'busy' => [],
                            'freeNotFormatted' => [],
                        ]
                    ];

                    if (is_array($employeeSchedule['timetable']))
                    {
                        if (is_array($employeeSchedule['timetable']['free']))
                        {
                            foreach ($employeeSchedule['timetable']['free'] as $freeItems)
                            {
                                $preparedEmployeeData['timetable']['freeNotFormatted'] = array_merge(
                                    $preparedEmployeeData['timetable']['freeNotFormatted'], $freeItems
                                );
                            }
                        }

                        if (is_array($employeeSchedule['timetable']['busy']))
                        {
                            foreach ($employeeSchedule['timetable']['busy'] as $busyItems)
                            {
                                $preparedEmployeeData['timetable']['busy'] = array_merge(
                                    $preparedEmployeeData['timetable']['busy'], $busyItems
                                );
                            }
                        }

                        if (is_array($employeeSchedule['timetable']['freeFormatted']))
                        {
                            foreach ($employeeSchedule['timetable']['freeFormatted'] as $freeFormattedItems)
                            {
                                $preparedEmployeeData['timetable']['free'] = array_merge(
                                    $preparedEmployeeData['timetable']['free'], $freeFormattedItems
                                );
                            }
                        }
                    }
                    $preparedData[] = $preparedEmployeeData;
                }
                unset($schedule[$clinicUid][$specialtyUid]);
            }
        }
        unset($schedule);
        return $preparedData;
    }

    /**
     * @param string|null $specialtyName
     * @return string
     */
    public static function getSpecialtyUid(?string $specialtyName): string
    {
        return !empty($specialtyName) ? preg_replace("/[^a-z0-9\s]/", '', strtolower(base64_encode($specialtyName))) : '';
    }
}