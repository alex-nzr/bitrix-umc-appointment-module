<?php

namespace ANZ\Appointment\Integration\UmcSdk\Mapper;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Dto\AppointmentDto;
use ANZ\Appointment\Dto\WaitListDto;
use ANZ\BitUmc\SDK\Domain\Request\BookAppointmentRequest;
use ANZ\BitUmc\SDK\Domain\Request\ReserveRequest;
use ANZ\BitUmc\SDK\Domain\Request\WaitListRequest;
use Bitrix\Main\Localization\Loc;
use DateTime;

class SdkRequestFromParams
{
    private const MIN_CALCULATED_DURATION_SECONDS = 1;

    /**
     * @throws \Exception
     */
    public function bookingItemFromParams(string $clinicUid, string $employeeUid, DateTime $dateTimeBegin, int $serviceDuration): ReserveRequest
    {
        return new ReserveRequest($clinicUid, $employeeUid, $dateTimeBegin);
    }

    /**
     * @throws \Exception
     */
    public function appointmentDtoFromArray(array $data): AppointmentDto
    {
        if ((int)$data['serviceDuration'] > 0)
        {
            $serviceDuration = (int)$data['serviceDuration'];
        }
        elseif(!empty($data['timeBegin']) && !empty($data['timeEnd']))
        {
            $startTimestamp = (new DateTime($data['timeBegin']))->getTimestamp();
            $endTimestamp = (new DateTime($data['timeEnd']))->getTimestamp();
            $serviceDuration = max(self::MIN_CALCULATED_DURATION_SECONDS, abs($endTimestamp - $startTimestamp));
        }
        else
        {
            $serviceDuration = Configuration::getInstance()->getDefaultAppointmentDuration();
        }
        return new AppointmentDto(
            (string)$data['bookingUid'],
            (string)$data['clinicUid'],
            (string)$data['employeeUid'],
            $this->serviceUidsFromArray($data),
            $serviceDuration,
            new DateTime((string)$data['timeBegin']),
            (string)$data['phone'],
            (string)$data['surname'],
            (string)$data['name'],
            (string)($data['middleName'] ?? ''),
            (string)$data['email'],
            key_exists('birthday', $data) && !empty($data['birthday']) ? new DateTime((string)$data['birthday']) : null,
            key_exists('address', $data) ? (string)$data['address'] : null,
            (string)$data['comment'],
        );
    }

    private function serviceUidsFromArray(array $data): array
    {
        $uids = [];
        if (!empty($data['serviceUid']))
        {
            $uids[] = (string)$data['serviceUid'];
        }
        if (is_array($data['services'] ?? null))
        {
            foreach ($data['services'] as $service)
            {
                if (is_array($service) && !empty($service['uid']))
                {
                    $uids[] = (string)$service['uid'];
                }
            }
        }
        return array_values(array_unique(array_filter($uids)));
    }

    /**
     * @throws \Exception
     */
    public function appointmentItemFromDto(AppointmentDto $dto): BookAppointmentRequest
    {
        return new BookAppointmentRequest(
            $dto->clinicUid,
            $dto->employeeUid,
            $dto->name,
            $dto->lastName,
            $dto->secondName !== '' ? $dto->secondName : ' ',
            $dto->phone,
            $dto->dateTimeBegin,
            '',
            $dto->email ?? '',
            $dto->address ?? '',
            $dto->comment ?? '',
            $dto->uid,
            $dto->birthday,
            $dto->serviceDuration,
            $dto->services
        );
    }

    /**
     * @throws \Exception
     */
    public function waitListDtoFromArray(array $data): WaitListDto
    {
        return new WaitListDto(
            (string)$data['clinicUid'],
            (string)$data['clinicName'],
            (string)$data['employeeUid'],
            (string)$data['doctorName'],
            (string)$data['specialty'],
            !empty($data['serviceName']) ? [(string)$data['serviceName']] : [],
            new DateTime((string)$data['timeBegin']),
            (string)$data['phone'],
            (string)$data['surname'],
            (string)$data['name'],
            (string)($data['middleName'] ?? ''),
            (string)$data['email'],
            (string)$data['comment'],
        );
    }

    /**
     * @throws \Exception
     */
    public function waitListItemFromDto(WaitListDto $dto): WaitListRequest
    {
        $comment = Loc::getMessage('ANZ_APPOINTMENT_WAITING_LIST_COMMENT', [
            '#CLINIC#' => $dto->clinicName,
            '#SPECIALTY#' => $dto->specialtyName,
            '#EMPLOYEE#' => $dto->employeeName,
            '#SERVICES#' => implode(', ', $dto->services),
            '#FULL_NAME#' => "$dto->lastName $dto->name $dto->secondName",
            '#PHONE#'     => $dto->phone,
            '#DATE#'      => $dto->dateTimeBegin->format('d.m.Y'),
            '#TIME#'      => $dto->dateTimeBegin->format('H:i'),
            '#COMMENT#'   => $dto->comment ?? '',
        ]);

        return new WaitListRequest(
            $dto->clinicUid,
            $dto->name,
            $dto->lastName,
            $dto->secondName !== '' ? $dto->secondName : ' ',
            $dto->phone,
            $dto->dateTimeBegin,
            $dto->specialtyName,
            $dto->email ?? '',
            '',
            $comment
        );
    }
}
