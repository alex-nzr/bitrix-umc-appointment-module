<?php

namespace ANZ\Appointment\Service\Security;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Dto\EmployeeDto;
use ANZ\Appointment\Dto\ScheduleItemDto;
use ANZ\Appointment\Service\Exchange\Manager;
use Bitrix\Main\ArgumentException;
use DateTime;

class AppointmentPayloadGuard
{
    private const MIN_DURATION_SECONDS = 300;
    private const MAX_DURATION_SECONDS = 28800;

    public function assertBookingCanBeCreated(
        Manager $exchange,
        string $clinicUid,
        string $employeeUid,
        string $dateTimeBegin,
        int $serviceDuration
    ): int
    {
        $this->assertClinicAllowed($exchange, $clinicUid);
        $this->findEmployee($exchange, $employeeUid);
        $duration = $this->resolveDuration($serviceDuration);
        if (Configuration::getInstance()->isDemoModeOn())
        {
            return $duration;
        }

        $this->assertSlotExists($exchange, $clinicUid, $employeeUid, $dateTimeBegin, $duration);

        return $duration;
    }

    /**
     * @throws \Bitrix\Main\ArgumentException
     */
    public function assertAppointmentPayload(Manager $exchange, array $data, ?array $booking, bool $bookingRequired = true): void
    {
        foreach (['bookingUid', 'clinicUid', 'employeeUid', 'timeBegin', 'phone', 'surname', 'name', 'middleName'] as $field)
        {
            if (trim((string)($data[$field] ?? '')) === '')
            {
                throw new ArgumentException('Invalid appointment data');
            }
        }

        if ($bookingRequired && $booking === null)
        {
            throw new ArgumentException('Booking is not found');
        }

        if ($booking !== null)
        {
            foreach (['clinicUid', 'employeeUid'] as $field)
            {
                if ((string)($booking[$field] ?? '') !== (string)$data[$field])
                {
                    throw new ArgumentException('Booking data mismatch');
                }
            }

            if (!empty($booking['timeBegin']) && strtotime((string)$booking['timeBegin']) !== strtotime((string)$data['timeBegin']))
            {
                throw new ArgumentException('Booking time mismatch');
            }
        }

        $this->assertClinicAllowed($exchange, (string)$data['clinicUid']);
        $employee = $this->findEmployee($exchange, (string)$data['employeeUid']);
        if (Configuration::getInstance()->isServicesEnabled())
        {
            $this->assertServicesBelongToEmployee($employee, $this->getServiceUids($data));
        }
        $this->resolveDuration((int)($data['serviceDuration'] ?? 0));
    }

    private function assertClinicAllowed(Manager $exchange, string $clinicUid): void
    {
        foreach ($exchange->getClinics() as $clinic)
        {
            if ($clinic->uid === $clinicUid)
            {
                return;
            }
        }
        throw new ArgumentException('Invalid clinic');
    }

    private function findEmployee(Manager $exchange, string $employeeUid): EmployeeDto
    {
        foreach ($exchange->getEmployees() as $employee)
        {
            if ($employee->uid === $employeeUid)
            {
                return $employee;
            }
        }
        throw new ArgumentException('Invalid employee');
    }

    private function assertServicesBelongToEmployee(EmployeeDto $employee, array $serviceUids): void
    {
        if (empty($serviceUids) || empty($employee->services))
        {
            return;
        }

        $employeeServiceUids = array_map(static fn($service) => $service->uid, $employee->services);
        foreach ($serviceUids as $serviceUid)
        {
            if (!in_array($serviceUid, $employeeServiceUids, true))
            {
                throw new ArgumentException('Invalid service');
            }
        }
    }

    private function assertSlotExists(Manager $exchange, string $clinicUid, string $employeeUid, string $dateTimeBegin, int $duration): void
    {
        $schedule = $exchange->getSchedule(Configuration::getInstance()->getExchangeSchedulePeriod(), $clinicUid, [$employeeUid]);
        $timestamp = strtotime($dateTimeBegin);
        if ($timestamp === false)
        {
            throw new ArgumentException('Invalid date');
        }

        foreach ($schedule as $clinicData)
        {
            foreach ((array)$clinicData as $specialtyData)
            {
                foreach ((array)$specialtyData as $scheduleItem)
                {
                    if ($scheduleItem instanceof ScheduleItemDto)
                    {
                        foreach ((array)($scheduleItem->timeslots['freeFormatted'] ?? []) as $dateSlots)
                        {
                            foreach ((array)$dateSlots as $slot)
                            {
                                if (strtotime($slot->timeBegin) === $timestamp)
                                {
                                    return;
                                }
                            }
                        }
                        foreach ((array)($scheduleItem->timeslots['free'] ?? []) as $dateSlots)
                        {
                            foreach ((array)$dateSlots as $slot)
                            {
                                $slotStart = strtotime($slot->timeBegin);
                                $slotEnd = strtotime($slot->timeEnd);
                                if ($slotStart !== false && $slotEnd !== false && $timestamp >= $slotStart && ($timestamp + $duration) <= $slotEnd)
                                {
                                    return;
                                }
                            }
                        }
                    }
                }
            }
        }

        throw new ArgumentException('Slot is not available');
    }

    private function resolveDuration(int $duration): int
    {
        if ($duration <= 0)
        {
            $duration = Configuration::getInstance()->getDefaultAppointmentDuration();
        }

        if ($duration < self::MIN_DURATION_SECONDS || $duration > self::MAX_DURATION_SECONDS)
        {
            throw new ArgumentException('Invalid appointment duration');
        }

        return $duration;
    }

    private function getServiceUids(array $data): array
    {
        $uids = [];
        if (!empty($data['serviceUid']))
        {
            $uids[] = (string)$data['serviceUid'];
        }

        if (is_array($data['services'] ?? null))
        {
            foreach ($data['services'] as $service)
            {
                if (is_array($service) && !empty($service['uid']))
                {
                    $uids[] = (string)$service['uid'];
                }
            }
        }

        return array_values(array_unique(array_filter($uids)));
    }
}
