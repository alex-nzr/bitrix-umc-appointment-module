<?php

namespace ANZ\Appointment\Tests\Unit\UI\Adapter;

use ANZ\Appointment\Config\TimeSlotStatus;
use ANZ\Appointment\Dto\TimeSlotDto;
use ANZ\Appointment\UI\Adapter\ReactMUI;
use DateTime;
use PHPUnit\Framework\TestCase;

class ReactMUITest extends TestCase
{
    public function testSplitTimeSlotExcludesIncompleteInterval(): void
    {
        $slots = ReactMUI::splitTimeSlot(
            $this->createFreeSlot('2026-03-26 19:30:00', '2026-03-26 22:00:00'),
            'clinic-1',
            'employee-1',
            5400
        );

        $this->assertCount(1, $slots);
        $this->assertSame('19:30', $slots[0]['formattedTimeBegin']);
        $this->assertSame('21:00', $slots[0]['formattedTimeEnd']);
    }

    public function testSplitTimeSlotReturnsNoSlotsWhenIntervalIsShorterThanAppointment(): void
    {
        $slots = ReactMUI::splitTimeSlot(
            $this->createFreeSlot('2026-03-26 19:30:00', '2026-03-26 20:00:00'),
            'clinic-1',
            'employee-1',
            5400
        );

        $this->assertSame([], $slots);
    }

    private function createFreeSlot(string $timeBegin, string $timeEnd): TimeSlotDto
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
            TimeSlotStatus::FREE
        );
    }
}
