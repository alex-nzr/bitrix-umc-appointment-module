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
            $arEmployee['isActive'] = true;
            $arEmployee['inSchedule'] = true;
            if ($employee->specialtyUid)
            {
                $arEmployee['specialties'] = [
                    $employee->specialtyUid => [
                        'uid' => $employee->specialtyUid,
                        'name' => $employee->specialtyName,
                        'isMain' => true
                    ]
                ];
            }

            if (!$employee->clinicUid)
            {
                $arEmployee['clinicUid'] = $defaultClinicUid;
            }

            $arEmployee['clinicName'] = '';
            foreach (static::$clinics as $clinic)
            {
                if ($clinic['uid'] === $arEmployee['clinicUid'])
                {
                    $arEmployee['clinicName'] = $clinic['name'];
                }
            }

            $preparedData[] = $arEmployee;
        }

        return $preparedData;
    }

    public static function prepareServicesData(array $services = [], array $employees = []): array
    {
        $preparedData = [];
        /** @var ServiceDto $service */
        foreach ($services as $service)
        {
            $arService = $service->toArray();
            $arService['specialties'] = [];
            /** @var EmployeeDto $employee */
            foreach ($employees as $employee)
            {
                foreach ($employee->services as $empService)
                {
                    if ($empService->uid === $service->uid)
                    {
                        $arService['specialties'][$employee->specialtyUid] = [
                            'uid' => $employee->specialtyUid,
                            'name' => $employee->specialtyName
                        ];
                        break;
                    }
                }
            }

            $preparedData[] = $arService;
        }

        return $preparedData;
    }

    public static function prepareScheduleData(array $schedule)
    {
        //todo method
    }

    private static function getDefaultClinicUid(): string
    {
        static $defaultClinicUid = null;
        if (is_null($defaultClinicUid))
        {
            $selectedClinics = Configuration::getInstance()->getSelectedClinics();
            $defaultClinicUid = count($selectedClinics) === 1 ? (string)current($selectedClinics) : '';
        }
        return $defaultClinicUid;
    }
}