<?php

namespace ANZ\Appointment\Service\Security;

class RateLimitPolicy
{
    private const BOOK_SLOT_LIMIT = 15;
    private const BOOK_SLOT_TTL = 600;
    private const SEND_APPOINTMENT_LIMIT = 10;
    private const SEND_APPOINTMENT_TTL = 900;
    private const CANCEL_APPOINTMENT_LIMIT = 10;
    private const CANCEL_APPOINTMENT_TTL = 900;

    public function __construct(private readonly RateLimiter $rateLimiter)
    {
    }

    public function assertBookSlotAllowed(string $clinicUid, string $employeeUid): void
    {
        $this->rateLimiter->assertAllowed(
            'book_slot',
            self::BOOK_SLOT_LIMIT,
            self::BOOK_SLOT_TTL,
            $clinicUid . '|' . $employeeUid
        );
    }

    public function assertSendAppointmentAllowed(): void
    {
        $this->rateLimiter->assertAllowed(
            'send_appointment',
            self::SEND_APPOINTMENT_LIMIT,
            self::SEND_APPOINTMENT_TTL
        );
    }

    public function assertCancelAppointmentAllowed(string $uid): void
    {
        $this->rateLimiter->assertAllowed(
            'cancel_appointment',
            self::CANCEL_APPOINTMENT_LIMIT,
            self::CANCEL_APPOINTMENT_TTL,
            $uid
        );
    }
}
