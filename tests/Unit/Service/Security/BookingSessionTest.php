<?php

namespace ANZ\Appointment\Tests\Unit\Service\Security;

use ANZ\Appointment\Service\Security\BookingSession;
use Bitrix\Main\SystemException;
use PHPUnit\Framework\TestCase;

class BookingSessionTest extends TestCase
{
    public function testAllowsEmailNoteOnlyForCreatedAppointmentAndMatchingEmail(): void
    {
        $bookingSession = new BookingSession();
        $bookingSession->rememberBooking(['uid' => 'booking-1']);
        $bookingSession->markAppointmentCreated('booking-1', ['email' => 'client@example.com']);

        $bookingSession->assertCanSendEmailNote('CLIENT@example.com');

        $this->addToAssertionCount(1);
    }

    public function testRejectsEmailNoteForUnrelatedEmail(): void
    {
        $this->expectException(SystemException::class);

        (new BookingSession())->assertCanSendEmailNote('unknown@example.com');
    }
}
