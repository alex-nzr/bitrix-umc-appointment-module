<?php

namespace ANZ\Appointment\Service\Security\Appointment;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Core\Exception\PublicRequestException;
use Bitrix\Main\Application;

class BookingSession
{
    private const KEY = 'anz_appointment_bookings';
    private const TTL = 900;

    public function rememberBooking(array $data): void
    {
        $bookings = $this->all();
        $uid = (string)($data['uid'] ?? $data['bookingUid'] ?? '');
        if ($uid === '')
        {
            return;
        }

        $bookings[$uid] = array_merge($bookings[$uid] ?? [], $data, [
            'uid' => $uid,
            'createdAt' => (int)($bookings[$uid]['createdAt'] ?? time()),
        ]);

        $this->setAll($bookings);
    }

    public function markAppointmentCreated(string $uid, array $data): void
    {
        $booking = $this->get($uid) ?? [];
        $booking['appointmentCreated'] = true;
        $booking['phone'] = (string)($data['phone'] ?? $booking['phone'] ?? '');
        $booking['email'] = (string)($data['email'] ?? $booking['email'] ?? '');
        $booking['timeBegin'] = (string)($data['timeBegin'] ?? $booking['timeBegin'] ?? '');
        $this->rememberBooking(array_merge($booking, ['uid' => $uid]));
    }

    public function markCaptchaVerified(string $uid): void
    {
        $booking = $this->getActiveBooking($uid);
        $booking['captchaVerified'] = true;
        $this->rememberBooking($booking);
    }

    /**
     * @throws \Bitrix\Main\SystemException
     */
    public function assertCanConfirm(string $uid): void
    {
        $booking = $this->getActiveBooking($uid);
        if (Configuration::getInstance()->isCaptchaEnabled() && !($booking['captchaVerified'] ?? false))
        {
            throw new PublicRequestException('CAPTCHA confirmation is required');
        }
    }

    /**
     * @throws \Bitrix\Main\SystemException
     */
    public function bindConfirmationTarget(string $uid, string $target): void
    {
        $booking = $this->getActiveBooking($uid);
        $boundTarget = (string)($booking['confirmationTarget'] ?? '');
        if ($boundTarget !== '' && !hash_equals($boundTarget, $target))
        {
            throw new PublicRequestException('Confirmation target does not match booking');
        }

        $booking['confirmationTarget'] = $target;
        $this->rememberBooking($booking);
    }

    /**
     * @throws \Bitrix\Main\SystemException
     */
    public function clearConfirmationTarget(string $uid): void
    {
        $booking = $this->getActiveBooking($uid);
        unset($booking['confirmationTarget']);
        $bookings = $this->all();
        $bookings[$uid] = $booking;
        $this->setAll($bookings);
    }

    /**
     * @throws \Bitrix\Main\SystemException
     */
    public function assertCanSendEmailNote(string $email): void
    {
        $email = strtolower(trim($email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            throw new PublicRequestException('Invalid email');
        }

        foreach ($this->all() as $booking)
        {
            if (($booking['appointmentCreated'] ?? false) !== true)
            {
                continue;
            }

            $bookingEmail = strtolower(trim((string)($booking['email'] ?? '')));
            if ($bookingEmail !== '' && hash_equals($bookingEmail, $email))
            {
                return;
            }
        }

        throw new PublicRequestException('Email note is not available for this appointment');
    }

    public function get(string $uid): ?array
    {
        $bookings = $this->all();
        return is_array($bookings[$uid] ?? null) ? $bookings[$uid] : null;
    }

    public function forget(string $uid): void
    {
        $bookings = $this->all();
        unset($bookings[$uid]);
        $this->setAll($bookings);
    }

    public function all(): array
    {
        $data = Application::getInstance()->getSession()->get(self::KEY);
        return is_array($data) ? $data : [];
    }

    private function setAll(array $bookings): void
    {
        Application::getInstance()->getSession()->set(self::KEY, $bookings);
    }

    /**
     * @throws \Bitrix\Main\SystemException
     */
    private function getActiveBooking(string $uid): array
    {
        $booking = $this->get($uid);
        if ($booking === null || ($booking['appointmentCreated'] ?? false) === true)
        {
            throw new PublicRequestException('Booking is not available');
        }

        if ((int)($booking['createdAt'] ?? 0) + self::TTL < time())
        {
            $this->forget($uid);
            throw new PublicRequestException('Booking has expired');
        }

        return $booking;
    }
}
