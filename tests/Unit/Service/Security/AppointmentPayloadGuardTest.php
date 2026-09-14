<?php

namespace ANZ\Appointment\Tests\Unit\Service\Security;

use ANZ\Appointment\Config\TimeSlotStatus;
use ANZ\Appointment\Dto\ScheduleItemDto;
use ANZ\Appointment\Dto\TimeSlotDto;
use ANZ\Appointment\Service\Exchange\Manager;
use ANZ\Appointment\Service\Security\Appointment\AppointmentPayloadGuard;
use ANZ\Appointment\Tests\Unit\Integration\UmcSdk\Gateway\FakeSdk;
use ANZ\Appointment\Tests\Unit\Model\FakeRecordTable;
use ANZ\Appointment\Tests\Unit\Repository\UMC\FakeAppointmentRepository;
use Bitrix\Main\ArgumentException;
use DateTime;
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

    public function testAssertBookingCanBeCreatedRejectsFormattedSlotThatExceedsFreeInterval(): void
    {
        $gateway = new FakeSdk();
        $gateway->schedule = [
            'clinic-1' => [
                'specialty-1' => [
                    'employee-1' => new ScheduleItemDto(
                        'clinic-1',
                        'specialty-1',
                        'employee-1',
                        'Therapist',
                        'Ivanov Ivan Ivanovich',
                        5400,
                        [
                            TimeSlotStatus::FREE->value => [
                                '2026-03-26' => [
                                    $this->createSlot('2026-03-26 19:30:00', '2026-03-26 22:00:00', TimeSlotStatus::FREE),
                                ],
                            ],
                            TimeSlotStatus::FREE_FORMATTED->value => [
                                '2026-03-26' => [
                                    $this->createSlot('2026-03-26 21:00:00', '2026-03-26 21:30:00', TimeSlotStatus::FREE_FORMATTED),
                                ],
                            ],
                        ]
                    ),
                ],
            ],
        ];
        $exchange = new Manager($gateway, new FakeAppointmentRepository(FakeRecordTable::getEntity()));

        $this->expectException(ArgumentException::class);

        (new AppointmentPayloadGuard())->assertBookingCanBeCreated(
            $exchange,
            'clinic-1',
            'employee-1',
            '2026-03-26 21:00:00',
            5400
        );
    }

    private function createSlot(string $timeBegin, string $timeEnd, TimeSlotStatus $status): TimeSlotDto
    {
        return new TimeSlotDto(
            '',
            '2026-03-26',
            $timeBegin,
            $timeEnd,
            '26.03.2026',
            (new DateTime($timeBegin))->format('H:i'),
            (new DateTime($timeEnd))->format('H:i'),
            new DateTime($timeBegin),
            $status
        );
    }
}
