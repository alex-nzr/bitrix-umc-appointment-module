<?php

namespace ANZ\Appointment\Tests\Unit\Integration\UmcSdk\Gateway;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Config\TimeSlotStatus;
use ANZ\Appointment\Dto\AppointmentDto;
use ANZ\Appointment\Dto\AppointmentStatusDto;
use ANZ\Appointment\Dto\BookingDto;
use ANZ\Appointment\Dto\ClinicDto;
use ANZ\Appointment\Dto\EmployeeServiceDto;
use ANZ\Appointment\Dto\EmployeeDto;
use ANZ\Appointment\Dto\ScheduleItemDto;
use ANZ\Appointment\Dto\ServiceDto;
use ANZ\Appointment\Dto\TimeSlotDto;
use ANZ\Appointment\Dto\WaitListDto;
use ANZ\Appointment\Integration\UmcSdk\Contract\UmcGatewayInterface;
use DateTime;
use Throwable;

class FakeSdk implements UmcGatewayInterface
{

    public ?Throwable $toThrow = null;
    public array $clinics;
    public array $employees;
    public array $services;
    public array $schedule;
    public string $statusCode = '000';
    public string $statusName = 'Fake status';

    public function __construct()
    {
        $this->clinics = [
            new ClinicDto('uniq-uid-12345', 'testClinic'),
            new ClinicDto('clinic-1', 'Test clinic for booking'),
        ];
        $this->employees = [
            new EmployeeDto(
                'employee-1',
                'Ivan',
                'Ivanov',
                'Ivanovich',
                'Ivanov Ivan Ivanovich',
                'clinic-1',
                '',
                '',
                '',
                'Therapist',
                'specialty-1',
                [new EmployeeServiceDto('service-1', 1800)]
            )
        ];
        $this->services = [
            new ServiceDto('service-1', 'Consultation', 'service', 'ART-1', 1000, 30, 'pcs', '')
        ];
        $slotDateTime = new DateTime('2026-03-26 10:00:00');
        $this->schedule = [
            'clinic-1' => [
                'specialty-1' => [
                    new ScheduleItemDto(
                        'clinic-1',
                        'specialty-1',
                        'employee-1',
                        'Therapist',
                        'Ivanov Ivan Ivanovich',
                        1800,
                        [
                            TimeSlotStatus::FREE_FORMATTED->value => [
                                '2026-03-26' => [
                                    new TimeSlotDto(
                                        '',
                                        '2026-03-26',
                                        '2026-03-26 10:00:00',
                                        '2026-03-26 10:30:00',
                                        '26.03.2026',
                                        '10:00',
                                        '10:30',
                                        $slotDateTime,
                                        TimeSlotStatus::FREE_FORMATTED
                                    ),
                                ],
                            ],
                        ]
                    ),
                ],
            ],
        ];
    }

    private function throwIfNeeded(): void
    {
        if (!is_null($this->toThrow))
        {
            throw $this->toThrow;
        }
    }

    public function checkConnection(string $strModeVal, string $url, string $login, string $password, string $token = ''): bool
    {
        $this->throwIfNeeded();
        return true;
    }

    public function getClinics(): array
    {
        $this->throwIfNeeded();
        return $this->clinics;
    }

    /**
     * @inheritDoc
     */
    public function getEmployees(): array
    {
        $this->throwIfNeeded();
        return $this->employees;
    }

    /**
     * @inheritDoc
     */
    public function getServices(string $clinicUid): array
    {
        $this->throwIfNeeded();
        return $this->services;
    }

    public function getSchedule(int $days = 14, string $clinicUid = '', array $employees = [], ?DateTime $startDate = null): array
    {
        $this->throwIfNeeded();
        return $this->schedule;
    }

    public function getAppointmentStatus(string $appointmentUid): AppointmentStatusDto
    {
        $this->throwIfNeeded();
        return new AppointmentStatusDto($this->statusCode, $this->statusName);
    }

    public function sendBooking(string $clinicUid, string $employeeUid, DateTime $dateTimeBegin, int $serviceDuration): BookingDto
    {
        $this->throwIfNeeded();
        return new BookingDto(
            uniqid('fake_uid_'),
            $clinicUid,
            $employeeUid,
            $dateTimeBegin->format(Configuration::DATE_FORMAT_FOR_OPTIONS),
            $serviceDuration
        );
    }

    public function sendWaitList(array $data): WaitListDto
    {
        $this->throwIfNeeded();

        return new WaitListDto(
            (string)$data['clinicUid'],
            (string)($data['clinicName'] ?? 'Clinic'),
            (string)$data['employeeUid'],
            (string)($data['doctorName'] ?? 'Doctor'),
            (string)($data['specialty'] ?? 'Specialty'),
            !empty($data['serviceName']) ? [(string)$data['serviceName']] : [],
            new DateTime((string)$data['timeBegin']),
            (string)$data['phone'],
            (string)$data['surname'],
            (string)$data['name'],
            (string)$data['middleName'],
            (string)($data['email'] ?? ''),
            (string)($data['comment'] ?? '')
        );
    }

    public function sendAppointment(array $data): AppointmentDto
    {
        $this->throwIfNeeded();

        return new AppointmentDto(
            (string)$data['bookingUid'],
            (string)$data['clinicUid'],
            (string)$data['employeeUid'],
            !empty($data['serviceUid']) ? [(string)$data['serviceUid']] : [],
            (int)($data['serviceDuration'] ?? 30),
            new DateTime((string)$data['timeBegin']),
            (string)$data['phone'],
            (string)$data['surname'],
            (string)$data['name'],
            (string)$data['middleName'],
            (string)($data['email'] ?? ''),
            null,
            (string)($data['address'] ?? ''),
            (string)($data['comment'] ?? '')
        );
    }

    public function deleteAppointment(string $uid): bool
    {
        $this->throwIfNeeded();
        return true;
    }
}
