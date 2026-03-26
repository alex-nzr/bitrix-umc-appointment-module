<?php

namespace ANZ\Appointment\UI\Adapter;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Config\TimeSlotStatus;
use ANZ\Appointment\Dto\ClinicDto;
use ANZ\Appointment\Dto\EmployeeDto;
use ANZ\Appointment\Dto\ScheduleItemDto;
use ANZ\Appointment\Dto\ServiceDto;
use ANZ\Appointment\Dto\TimeSlotDto;
use ANZ\Appointment\Service\Container;

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
            $arEmployee['services'] = [];
            foreach ($employee->services as $service)
            {
                $arEmployee['services'][$service->uid] = $service->toArray();
            }
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

    /**
     * @throws \Exception
     */
    public static function prepareScheduleData(array $schedule, array $serviceUIDs = []): array
    {
        $employees = Container::getInstance()->getExchangeManager()->getEmployees();
        $employeesByUid = [];
        /** @var EmployeeDto $employee */
        foreach ($employees as $employee)
        {
            $employeesByUid[$employee->uid] = $employee;
        }

        $preparedData = [];
        foreach ($schedule as $clinicUid => $clinicData)
        {
            $services = Container::getInstance()->getExchangeManager()->getServices($clinicUid);
            $servicesByUid = [];
            /** @var ServiceDto $service */
            foreach ($services as $service)
            {
                $servicesByUid[$service->uid] = $service;
            }
            foreach ($clinicData as $specialtyData)
            {
                /** @var ScheduleItemDto $scheduleItem */
                foreach ($specialtyData as $employeeUid => $scheduleItem)
                {
                    $duration = static::resolveScheduleItemDuration(
                        $scheduleItem->durationInSeconds,
                        $serviceUIDs,
                        $employeesByUid[$employeeUid] ?? null,
                        $servicesByUid
                    );

                    if (is_array($scheduleItem->timeslots[TimeSlotStatus::FREE->value]))
                    {
                        foreach ($scheduleItem->timeslots[TimeSlotStatus::FREE->value] as $timeslots)
                        {
                            /** @var TimeSlotDto $timeslot */
                            foreach ($timeslots as $timeslot)
                            {
                                $preparedData = array_merge(
                                    $preparedData,
                                    static::splitTimeSlot($timeslot, $clinicUid, $employeeUid, $duration)
                                );
                            }
                        }
                    }
                }
            }
        }
        return $preparedData;
    }

    /**
     * @param string[] $serviceUIDs
     * @param array<string, ServiceDto> $servicesByUid
     */
    private static function resolveScheduleItemDuration(
        int $defaultDuration,
        array $serviceUIDs,
        ?EmployeeDto $employee,
        array $servicesByUid
    ): int
    {
        $resolvedDuration = 0;

        if (!empty($serviceUIDs))
        {
            foreach ($serviceUIDs as $serviceUid)
            {
                $serviceDuration = 0;

                if ($employee instanceof EmployeeDto)
                {
                    foreach ($employee->services as $employeeService)
                    {
                        if ($employeeService->uid === $serviceUid && $employeeService->personalDuration > 0)
                        {
                            $serviceDuration = $employeeService->personalDuration;
                            break;
                        }
                    }
                }

                if (
                    $serviceDuration <= 0
                    &&
                    isset($servicesByUid[$serviceUid])
                    && $servicesByUid[$serviceUid] instanceof ServiceDto
                    && $servicesByUid[$serviceUid]->duration > 0
                )
                {
                    $serviceDuration = $servicesByUid[$serviceUid]->duration;
                }

                if ($serviceDuration > 0)
                {
                    $resolvedDuration += $serviceDuration;
                }
            }
        }

        return $resolvedDuration > 0 ? $resolvedDuration : $defaultDuration;
    }

    /**
     * @throws \Exception
     */
    public static function splitTimeSlot(TimeSlotDto $timeSlot, string $clinicUid, string $doctorUid, int $duration = 0): array
    {
        if ($duration <= 0)
        {
            $duration = Configuration::getInstance()->getDefaultAppointmentDuration();
        }
        $timestampTimeBegin = (int)strtotime($timeSlot->timeBegin);
        $timestampTimeEnd = (int)strtotime($timeSlot->timeEnd);
        $timeDifference = $timestampTimeEnd - $timestampTimeBegin;
        $timeslotsCount = round($timeDifference / ($duration));

        $result = [];
        for ($i = 0; $i < $timeslotsCount; $i++)
        {
            $start = $timestampTimeBegin + ($duration * $i);
            $end = $timestampTimeBegin + ($duration * ($i+1));

            $result[] = [
                'clinicUid' => $clinicUid,
                'doctorUid' => $doctorUid,
                'isAvailable' => $timeSlot->status === TimeSlotStatus::FREE,
                'status' => $timeSlot->status->value,
                'date' => $timeSlot->date,
                'timeBegin' => date("Y-m-d", $start) ."T". date("H:i:s", $start),
                'timeEnd' => date("Y-m-d", $end) ."T". date("H:i:s", $end),
                'formattedDate' => $timeSlot->formattedDate,
                'formattedTimeBegin' => date("H:i", $start),
                'formattedTimeEnd' => date("H:i", $end),
                'typeOfTimeUid' => $timeSlot->typeOfTimeUid,
                'duration' => $end - $start,
            ];
        }

        return $result;
    }

    private static function getDefaultClinicUid(): string
    {
        static $defaultClinicUid = null;
        if (is_null($defaultClinicUid))
        {
            $defaultClinicUid = Configuration::getInstance()->getDefaultClinic();
            if (is_null($defaultClinicUid))
            {
                $selectedClinics = Configuration::getInstance()->getSelectedClinics();
                $defaultClinicUid = count($selectedClinics) === 1 ? (string)current($selectedClinics) : '';
            }
        }
        return $defaultClinicUid;
    }
}
