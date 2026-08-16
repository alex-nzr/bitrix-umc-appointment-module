<?php

namespace ANZ\Appointment\Tests\Unit\Service\Security;

use ANZ\Appointment\Service\Security\Appointment\BookingSession;
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

    public function testRejectsChangingConfirmationTargetForBooking(): void
    {
        $bookingSession = new BookingSession();
        $bookingSession->rememberBooking(['uid' => 'booking-confirm']);
        $bookingSession->markCaptchaVerified('booking-confirm');
        $bookingSession->bindConfirmationTarget('booking-confirm', 'client@example.com');

        $this->expectException(SystemException::class);
        $bookingSession->bindConfirmationTarget('booking-confirm', 'other@example.com');
    }

    public function testAllowsChangingConfirmationTargetOnlyAfterExplicitReset(): void
    {
        $bookingSession = new BookingSession();
        $bookingSession->rememberBooking(['uid' => 'booking-reset']);
        $bookingSession->markCaptchaVerified('booking-reset');
        $bookingSession->bindConfirmationTarget('booking-reset', 'client@example.com');
        $bookingSession->clearConfirmationTarget('booking-reset');
        $bookingSession->bindConfirmationTarget('booking-reset', 'other@example.com');

        $this->addToAssertionCount(1);
    }
}
