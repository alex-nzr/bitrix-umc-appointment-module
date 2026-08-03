<?php

namespace ANZ\Appointment\Tests\Unit\Service\Security;

use ANZ\Appointment\Service\Exchange\Manager;
use ANZ\Appointment\Service\Security\AppointmentPayloadGuard;
use ANZ\Appointment\Tests\Unit\Integration\UmcSdk\Gateway\FakeSdk;
use ANZ\Appointment\Tests\Unit\Model\FakeRecordTable;
use ANZ\Appointment\Tests\Unit\Repository\UMC\FakeAppointmentRepository;
use PHPUnit\Framework\TestCase;

class AppointmentPayloadGuardTest extends TestCase
{
    public function testAssertAppointmentPayloadAllowsMissingMiddleName(): void
    {
        $exchange = new Manager(
            new FakeSdk(),
            new FakeAppointmentRepository(FakeRecordTable::getEntity())
        );
        $booking = [
            'uid' => 'booking-1',
            'clinicUid' => 'clinic-1',
            'employeeUid' => 'employee-1',
            'timeBegin' => '2026-03-26 10:00:00',
            'serviceDuration' => 1800,
        ];

        (new AppointmentPayloadGuard())->assertAppointmentPayload($exchange, [
            'bookingUid' => $booking['uid'],
            'clinicUid' => $booking['clinicUid'],
            'employeeUid' => $booking['employeeUid'],
            'serviceUid' => 'service-1',
            'serviceDuration' => $booking['serviceDuration'],
            'timeBegin' => $booking['timeBegin'],
            'phone' => '+79990000000',
            'surname' => 'Ivanov',
            'name' => 'Ivan',
        ], $booking);

        $this->addToAssertionCount(1);
    }
}
