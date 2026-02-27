<?php

namespace ANZ\Appointment\UI\Adapter;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Dto\ClinicDto;
use ANZ\Appointment\Dto\EmployeeDto;
use ANZ\Appointment\Dto\ServiceDto;

class ReactMUI
{
    private static array $clinics = [];

    public static function prepareClinicsData(array $clinicsData = []): array
    {
        static::$clinics = array_map(fn(ClinicDto $clinicDto) => $clinicDto->toArray(), array_values($clinicsData));
        foreach (static::$clinics as $key => $clinic)
        {
            static::$clinics[$key]['isDefault'] = ($clinic['uid'] === static::getDefaultClinicUid());
        }
        return static::$clinics;
    }

    public static function prepareEmployeesData(array $employees = []): array
    {
        $preparedData = [];
        $defaultClinicUid = static::getDefaultClinicUid();
        /** @var EmployeeDto $employee */
        foreach ($employees as $employee)
        {
            $arEmployee = $employee->toArray();
            if ($employee->specialtyUid)
            {
                $arEmployee['specialties'] = [
                    $employee->specialtyUid => [
                        'name' => $employee->specialtyName
                    ]
                ];
            }

            if (!$employee->clinicUid)
            {
                $arEmployee['clinicUid'] = $defaultClinicUid;
            }

            $arEmployee['clinic'] = '';
            foreach (static::$clinics as $clinic)
            {
                if ($clinic['uid'] === $arEmployee['clinicUid'])
                {
                    $arEmployee['clinic'] = $clinic['name'];
                }
            }

            //todo logic
            /*if (is_array($employee['services']))
            {
                foreach ($employee['services'] as $serviceUid => $serviceData)
                {
                    $employee['services'][$serviceUid] = [
                        'title' => ''
                    ];
                }
            }*/

            $preparedData[$arEmployee['uid']] = $arEmployee;
        }

        return $preparedData;
    }

    private static function getDefaultClinicUid(): string
    {
        static $defaultClinicUid = null;
        if (is_null($defaultClinicUid))
        {
            $selectedClinics = Configuration::getInstance()->getSelectedClinics();
            $defaultClinicUid = count($selectedClinics) > 0 ? (string)current($selectedClinics) : '';
        }
        return strlen(trim($defaultClinicUid)) === 0 ? (string)current(static::$clinics)['uid'] : $defaultClinicUid;
    }
}