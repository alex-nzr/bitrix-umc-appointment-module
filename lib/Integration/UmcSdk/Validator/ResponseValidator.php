<?php

namespace ANZ\Appointment\Integration\UmcSdk\Validator;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Core\Exception\ValidatorException;
use ANZ\Appointment\Core\Trait\Validator;
use ANZ\Appointment\Dto\BookingDto;

class ResponseValidator
{
    use Validator;

    private array $clinicsFilter;

    public function __construct()
    {
        $this->clinicsFilter = Configuration::getInstance()->getSelectedClinics();
    }

    public function validateClinic(mixed $data): bool
    {
        if (is_array($data))
        {
            return key_exists('uid', $data) && !empty($data['uid'])
                    && key_exists('name', $data) && !empty($data['name'])
                    && (empty($this->clinicsFilter) || in_array($data['uid'], $this->clinicsFilter));
        }
        return false;
    }

    public function validateEmployee(mixed $data): bool
    {
        if (is_array($data))
        {
            return key_exists('uid', $data) && !empty($data['uid'])
                && key_exists('name', $data) && !empty($data['name']);
        }
        return false;
    }

    public function validateService(mixed $data): bool
    {
        if (is_array($data))
        {
            return key_exists('uid', $data) && !empty($data['uid'])
                && key_exists('name', $data) && !empty($data['name']);
        }
        return false;
    }

    public function validateScheduleItem(mixed $data): bool
    {
        if (is_array($data) && key_exists('timetable', $data) && is_array($data['timetable']))
        {
            return true;
        }
        return false;
    }

    /**
     * @throws ValidatorException
     */
    public function validateBookingItem(BookingDto $dto): bool
    {
        if (strlen($dto->uid) <= 0)
        {
            throw new ValidatorException('Booking uid is empty');
        }

        return true;
    }

    /**
     * @throws ValidatorException
     */
    public function validateAppointmentStatus(array $data): bool
    {
        if (!key_exists('statusId', $data) || empty($data['statusId']))
        {
            throw new ValidatorException('Status id is empty');
        }
        if (!key_exists('statusTitle', $data) || empty($data['statusTitle']))
        {
            throw new ValidatorException('Status title is empty');
        }

        return true;
    }
}
