<?php
namespace ANZ\Appointment\Integration\UmcSdk\Contract;

use ANZ\Appointment\Dto\AppointmentDto;
use ANZ\Appointment\Dto\AppointmentStatusDto;
use ANZ\Appointment\Dto\BookingDto;
use ANZ\Appointment\Dto\ClinicDto;
use ANZ\Appointment\Dto\EmployeeDto;
use ANZ\Appointment\Dto\ServiceDto;
use ANZ\Appointment\Dto\WaitListDto;
use DateTime;

interface UmcGatewayInterface
{
    public function checkConnection(string $strModeVal, string $url, string $login, string $password, string $token = ''): bool;

    /** @return ClinicDto[] */
    public function getClinics(): array;

    /** @return EmployeeDto[] */
    public function getEmployees(): array;

    /** @return ServiceDto[] */
    public function getServices(string $clinicUid): array;

    public function getSchedule(int $days = 14, string $clinicUid = '', array $employees = [], ?DateTime $startDate = null): array;

    public function getAppointmentStatus(string $appointmentUid): AppointmentStatusDto;

    public function sendBooking(string $clinicUid, string $employeeUid, DateTime $dateTimeBegin, int $serviceDuration): BookingDto;

    public function sendWaitList(array $data): WaitListDto;

    public function sendAppointment(array $data): AppointmentDto;

    public function deleteAppointment(string $uid): bool;
}
