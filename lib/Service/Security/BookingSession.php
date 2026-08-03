<?php

namespace ANZ\Appointment\Service\Security;

use Bitrix\Main\Application;

class BookingSession
{
    private const KEY = 'anz_appointment_bookings';

    public function rememberBooking(array $data): void
    {
        $bookings = $this->all();
        $uid = (string)($data['uid'] ?? $data['bookingUid'] ?? '');
        if ($uid === '')
        {
            return;
        }

        $bookings[$uid] = array_merge($data, [
            'uid' => $uid,
            'createdAt' => time(),
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

    /**
     * @throws \Bitrix\Main\SystemException
     */
    public function assertCanSendEmailNote(string $email): void
    {
        $email = strtolower(trim($email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            throw new \Bitrix\Main\SystemException('Invalid email');
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

        throw new \Bitrix\Main\SystemException('Email note is not available for this appointment');
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
}
