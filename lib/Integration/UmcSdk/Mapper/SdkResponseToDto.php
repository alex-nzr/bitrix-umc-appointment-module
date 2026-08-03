<?php
namespace ANZ\Appointment\Integration\UmcSdk\Mapper;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Config\TimeSlotStatus;
use ANZ\Appointment\Dto\AppointmentStatusDto;
use ANZ\Appointment\Dto\ClinicDto;
use ANZ\Appointment\Dto\EmployeeDto;
use ANZ\Appointment\Dto\EmployeeServiceDto;
use ANZ\Appointment\Dto\ScheduleItemDto;
use ANZ\Appointment\Dto\ServiceDto;
use ANZ\Appointment\Dto\TimeSlotDto;
use ANZ\Appointment\Integration\UmcSdk\Exception\SdkDataMapperException;
use DateTime;
use Throwable;

class SdkResponseToDto
{
    public function clinicFromArray(array $item): ClinicDto
    {
        return new ClinicDto(
            (string)$item['uid'],
            (string)$item['name'],
            $this->extraFromArray($item)
        );
    }

    public function employeeFromArray(array $item): EmployeeDto
    {
        return new EmployeeDto(
            (string)$item['uid'],
            (string)$item['name'],
            (string)$item['surname'],
            (string)$item['middleName'],
            (string)$item['fullName'],
            (string)$item['clinicUid'],
            (string)$item['photo'],
            (string)$item['description'],
            (string)$item['rating'],
            (string)$item['specialtyName'],
            (string)$item['specialtyUid'],
            array_map(
                fn (array $empService) => new EmployeeServiceDto(
                    (string)$empService['uid'],
                    (int)$empService['personalDuration'],
                    $this->extraFromArray($empService)
                ),
                is_array($item['services']) ? $item['services'] : []
            ),
            $this->extraFromArray($item),
        );
    }

    public function serviceFromArray(array $item): ServiceDto
    {
        return new ServiceDto(
            (string)$item['uid'],
            (string)$item['name'],
            (string)$item['typeOfItem'],
            (string)$item['artNumber'],
            (int)$item['price'],
            (int)$item['duration'],
            (string)$item['measureUnit'],
            (string)$item['parent'],
            $this->extraFromArray($item),
        );
    }

    /**
     * @throws SdkDataMapperException
     */
    public function scheduleItemFromArray(
        string $clinicUid, string $specialtyUid, string $employeeUid, array $scheduleData
    ): ScheduleItemDto
    {
        try
        {
            $timeslots = [];
            foreach ($scheduleData['timetable'] as $status => $statusData)
            {
                if (!key_exists($status, $timeslots))
                {
                    $timeslots[$status] = [];
                }
                foreach ($statusData as $date => $timeslotsData)
                {
                    foreach ($timeslotsData as $timeslot)
                    {
                        if (!key_exists($date, $timeslots[$status]))
                        {
                            $timeslots[$status][$date] = [];
                        }
                        $timeslots[$status][$date][] = $this->timeslotFromArray($timeslot, $status);
                    }
                }
            }
            $duration = (int)$scheduleData['durationInSeconds'];
            return new ScheduleItemDto(
                $clinicUid,
                $specialtyUid,
                $employeeUid,
                (string)$scheduleData['specialtyName'],
                (string)$scheduleData['employeeName'],
                $duration > 0 ? $duration : Configuration::getInstance()->getDefaultAppointmentDuration(),
                $timeslots,
                $this->extraFromArray($scheduleData)
            );
        }
        catch (Throwable $e)
        {
            throw new SdkDataMapperException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws \Exception
     */
    protected function timeslotFromArray(array $item, string $status): TimeSlotDto
    {
        return new TimeSlotDto(
            (string)$item['typeOfTimeUid'],
            (string)$item['date'],
            (string)$item['timeBegin'],
            (string)$item['timeEnd'],
            (string)$item['formattedDate'],
            (string)$item['formattedTimeBegin'],
            (string)$item['formattedTimeEnd'],
            new DateTime($item['timeBegin']),
            TimeSlotStatus::from($status),
            $this->extraFromArray($item)
        );
    }

    public function statusFromArray(array $data): AppointmentStatusDto
    {
        return new AppointmentStatusDto($data['statusId'], $data['statusTitle']);
    }

    private function extraFromArray(array $item): array
    {
        return (key_exists('_extra', $item) && is_array($item['_extra'])) ? $item['_extra'] : [];
    }
}
