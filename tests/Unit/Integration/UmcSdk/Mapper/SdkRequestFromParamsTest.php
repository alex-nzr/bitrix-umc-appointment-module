<?php

namespace ANZ\Appointment\Tests\Unit\Integration\UmcSdk\Mapper;

use ANZ\Appointment\Integration\UmcSdk\Mapper\SdkRequestFromParams;
use ANZ\BitUmc\SDK\Domain\Request\BookAppointmentRequest;
use ANZ\BitUmc\SDK\Domain\Request\ReserveRequest;
use PHPUnit\Framework\TestCase;

class SdkRequestFromParamsTest extends TestCase
{
    public function testAppointmentDtoUsesExplicitServiceDurationWhenItIsProvided(): void
    {
        $mapper = new SdkRequestFromParams();

        $dto = $mapper->appointmentDtoFromArray([
            'bookingUid' => 'booking-1',
            'clinicUid' => 'clinic-1',
            'employeeUid' => 'employee-1',
            'serviceUid' => 'service-1',
            'serviceDuration' => 2700,
            'timeBegin' => '2026-03-26 10:00:00',
            'phone' => '+79990000000',
            'surname' => 'Ivanov',
            'name' => 'Ivan',
            'middleName' => 'Ivanovich',
            'email' => 'test@example.com',
            'comment' => 'Test comment',
        ]);

        $this->assertSame(2700, $dto->serviceDuration);
        $this->assertSame(['service-1'], $dto->services);
    }

    public function testAppointmentDtoCalculatesDurationFromTimeRange(): void
    {
        $mapper = new SdkRequestFromParams();

        $dto = $mapper->appointmentDtoFromArray([
            'bookingUid' => 'booking-1',
            'clinicUid' => 'clinic-1',
            'employeeUid' => 'employee-1',
            'serviceUid' => 'service-1',
            'serviceDuration' => 0,
            'timeBegin' => '2026-03-26 10:00:00',
            'timeEnd' => '2026-03-26 11:15:00',
            'phone' => '+79990000000',
            'surname' => 'Ivanov',
            'name' => 'Ivan',
            'middleName' => 'Ivanovich',
            'email' => 'test@example.com',
            'comment' => 'Test comment',
        ]);

        $this->assertSame(4500, $dto->serviceDuration);
    }

    public function testAppointmentDtoAllowsMissingMiddleName(): void
    {
        $mapper = new SdkRequestFromParams();

        $dto = $mapper->appointmentDtoFromArray([
            'bookingUid' => 'booking-1',
            'clinicUid' => 'clinic-1',
            'employeeUid' => 'employee-1',
            'serviceUid' => 'service-1',
            'serviceDuration' => 1800,
            'timeBegin' => '2026-03-26 10:00:00',
            'phone' => '+79990000000',
            'surname' => 'Ivanov',
            'name' => 'Ivan',
            'email' => 'test@example.com',
            'comment' => 'Test comment',
        ]);

        $this->assertSame('', $dto->secondName);
    }

    public function testWaitListDtoMapsEmployeeUidSeparatelyFromClinicUid(): void
    {
        $mapper = new SdkRequestFromParams();

        $dto = $mapper->waitListDtoFromArray([
            'clinicUid' => 'clinic-1',
            'clinicName' => 'Clinic',
            'employeeUid' => 'employee-1',
            'doctorName' => 'Doctor',
            'specialty' => 'Therapist',
            'serviceName' => 'Consultation',
            'timeBegin' => '2026-03-26 10:00:00',
            'phone' => '+79990000000',
            'surname' => 'Ivanov',
            'name' => 'Ivan',
            'middleName' => 'Ivanovich',
            'email' => 'test@example.com',
            'comment' => 'Test comment',
        ]);

        $this->assertSame('clinic-1', $dto->clinicUid);
        $this->assertSame('employee-1', $dto->employeeUid);
    }

    public function testBookingItemFromParamsReturnsReserveRequest(): void
    {
        $mapper = new SdkRequestFromParams();

        $request = $mapper->bookingItemFromParams(
            'clinic-1',
            'employee-1',
            new \DateTime('2026-03-26 10:00:00'),
            1800
        );

        $this->assertInstanceOf(ReserveRequest::class, $request);
        $this->assertSame('clinic-1', $request->clinicUid);
        $this->assertSame('employee-1', $request->employeeUid);
    }

    public function testAppointmentItemFromDtoReturnsBookAppointmentRequest(): void
    {
        $mapper = new SdkRequestFromParams();

        $dto = $mapper->appointmentDtoFromArray([
            'bookingUid' => 'booking-1',
            'clinicUid' => 'clinic-1',
            'employeeUid' => 'employee-1',
            'serviceUid' => 'service-1',
            'serviceDuration' => 1800,
            'timeBegin' => '2026-03-26 10:00:00',
            'phone' => '+79990000000',
            'surname' => 'Ivanov',
            'name' => 'Ivan',
            'middleName' => '',
            'email' => 'test@example.com',
            'comment' => 'Test comment',
        ]);

        $request = $mapper->appointmentItemFromDto($dto);

        $this->assertInstanceOf(BookAppointmentRequest::class, $request);
        $this->assertSame('booking-1', $request->appointmentUid);
        $this->assertSame(['service-1'], $request->services);
        $this->assertSame(1800, $request->appointmentDuration);
        $this->assertSame(' ', $request->secondName);
    }
}
