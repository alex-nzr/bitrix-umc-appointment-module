<?php

namespace ANZ\Appointment\Service\Security\RateLimit;

class RateLimitPolicy
{
    private const BOOK_SLOT_LIMIT = 15;
    private const BOOK_SLOT_TTL = 600;
    private const SEND_APPOINTMENT_LIMIT = 10;
    private const SEND_APPOINTMENT_TTL = 900;
    private const CANCEL_APPOINTMENT_LIMIT = 10;
    private const CANCEL_APPOINTMENT_TTL = 900;
    private const EMAIL_NOTE_LIMIT = 5;
    private const EMAIL_NOTE_TTL = 900;
    private const CAPTCHA_ISSUE_LIMIT = 30;
    private const CAPTCHA_ISSUE_TTL = 900;
    private const CONFIRM_SEND_IP_LIMIT = 5;
    private const CONFIRM_SEND_TARGET_LIMIT = 3;
    private const CONFIRM_SEND_BOOKING_LIMIT = 2;
    private const CONFIRM_SEND_GLOBAL_LIMIT = 100;
    private const CONFIRM_SEND_TTL = 900;
    private const CONFIRM_SEND_GLOBAL_TTL = 3600;

    public function __construct(private readonly RateLimiter $rateLimiter)
    {
    }

    /**
     * @throws \Bitrix\Main\SystemException
     */
    public function assertBookSlotAllowed(string $clinicUid, string $employeeUid): void
    {
        $this->rateLimiter->assertAllowed(
            'book_slot',
            self::BOOK_SLOT_LIMIT,
            self::BOOK_SLOT_TTL,
            $clinicUid . '|' . $employeeUid
        );
    }

    /**
     * @throws \Bitrix\Main\SystemException
     */
    public function assertSendAppointmentAllowed(): void
    {
        $this->rateLimiter->assertAllowed(
            'send_appointment',
            self::SEND_APPOINTMENT_LIMIT,
            self::SEND_APPOINTMENT_TTL
        );
    }

    /**
     * @throws \Bitrix\Main\SystemException
     */
    public function assertCancelAppointmentAllowed(string $uid): void
    {
        $this->rateLimiter->assertAllowed(
            'cancel_appointment',
            self::CANCEL_APPOINTMENT_LIMIT,
            self::CANCEL_APPOINTMENT_TTL,
            $uid
        );
    }

    /**
     * @throws \Bitrix\Main\SystemException
     */
    public function assertEmailNoteAllowed(string $email): void
    {
        $this->rateLimiter->assertAllowed(
            'email_note',
            self::EMAIL_NOTE_LIMIT,
            self::EMAIL_NOTE_TTL,
            strtolower(trim($email))
        );
    }

    /**
     * @throws \Bitrix\Main\SystemException
     */
    public function assertCaptchaIssueAllowed(): void
    {
        $this->rateLimiter->assertAllowed(
            'captcha_issue',
            self::CAPTCHA_ISSUE_LIMIT,
            self::CAPTCHA_ISSUE_TTL
        );
    }

    /**
     * @throws \Bitrix\Main\SystemException
     */
    public function assertConfirmCodeSendAllowed(string $target, string $bookingUid): void
    {
        $this->rateLimiter->assertAllowed(
            'confirm_send_ip',
            self::CONFIRM_SEND_IP_LIMIT,
            self::CONFIRM_SEND_TTL
        );
        $this->rateLimiter->assertAllowed(
            'confirm_send_target',
            self::CONFIRM_SEND_TARGET_LIMIT,
            self::CONFIRM_SEND_TTL,
            $target,
            false
        );
        $this->rateLimiter->assertAllowed(
            'confirm_send_booking',
            self::CONFIRM_SEND_BOOKING_LIMIT,
            self::CONFIRM_SEND_TTL,
            $bookingUid
        );
        $this->rateLimiter->assertAllowed(
            'confirm_send_global',
            self::CONFIRM_SEND_GLOBAL_LIMIT,
            self::CONFIRM_SEND_GLOBAL_TTL,
            '',
            false
        );
    }
}
