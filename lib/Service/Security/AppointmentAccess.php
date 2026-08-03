<?php

namespace ANZ\Appointment\Service\Security;

use ANZ\Appointment\Model\RecordTable;
use ANZ\Appointment\Service\Container;
use Bitrix\Main\AccessDeniedException;
use Bitrix\Main\Type\DateTime;

class AppointmentAccess
{
    public function __construct(private readonly BookingSession $bookingSession)
    {
    }

    /**
     * @throws \Exception
     */
    public function assertCanCancelPublic(string $uid): ?array
    {
        $booking = $this->bookingSession->get($uid);
        if ($booking !== null && empty($booking['appointmentCreated']))
        {
            return $booking;
        }

        if ($booking !== null && !empty($booking['appointmentCreated']))
        {
            $timeBegin = (int)($booking['timeBegin'] ?? 0);
            if ($timeBegin > 0 && $timeBegin <= time())
            {
                throw new AccessDeniedException('Appointment time has already passed');
            }
            return $booking;
        }

        $record = RecordTable::getList([
            'filter' => ['=XML_ID' => $uid],
            'select' => ['*'],
            'limit' => 1,
        ])->fetch();

        if (!$record)
        {
            if ($booking !== null)
            {
                return $booking;
            }
            throw new AccessDeniedException('Access denied');
        }

        $userId = Container::getInstance()->getUserPermissions()->getUserId();
        if ($userId <= 0 || (int)$record[RecordTable::FIELD_NAME_USER_ID] !== $userId)
        {
            throw new AccessDeniedException('Access denied');
        }

        $visit = $record[RecordTable::FIELD_NAME_DATETIME_VISIT] ?? null;
        if ($visit instanceof DateTime && $visit->getTimestamp() <= time())
        {
            throw new AccessDeniedException('Appointment time has already passed');
        }

        return $record;
    }
}
