<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 08.09.2025
 * ==================================================
*/

namespace ANZ\Appointment\Integration\UmcSdk\Mapper;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Dto\AppointmentDto;
use ANZ\Appointment\Dto\WaitListDto;
use ANZ\BitUmc\SDK\Builder\Order as OrderBuilder;
use ANZ\BitUmc\SDK\Item\Order as OrderItem;
use Bitrix\Main\Localization\Loc;
use DateTime;

class SdkRequestFromParams
{
    /**
     * @throws \Exception
     */
    public function bookingItemFromParams(string $clinicUid, string $employeeUid, DateTime $dateTimeBegin, int $serviceDuration): OrderItem
    {
        return OrderBuilder::createReserve()
            ->setClinicUid($clinicUid)
            ->setEmployeeUid($employeeUid)
            ->setDateTimeBegin($dateTimeBegin)
            ->setAppointmentDuration($serviceDuration)
            ->build();
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
            $serviceDuration = max(1, (int)ceil(abs($endTimestamp - $startTimestamp) / 60));
        }
        else
        {
            $serviceDuration = Configuration::getInstance()->getDefaultAppointmentDuration();
        }
        return new AppointmentDto(
            (string)$data['bookingUid'],
            (string)$data['clinicUid'],
            (string)$data['employeeUid'],
            !empty($data['serviceUid']) ? [(string)$data['serviceUid']] : [],
            $serviceDuration,
            new DateTime((string)$data['timeBegin']),
            (string)$data['phone'],
            (string)$data['surname'],
            (string)$data['name'],
            (string)$data['middleName'],
            (string)$data['email'],
            key_exists('birthday', $data) && !empty($data['birthday']) ? new DateTime((string)$data['birthday']) : null,
            key_exists('address', $data) ? (string)$data['address'] : null,
            (string)$data['comment'],
        );
    }

    /**
     * @throws \Exception
     */
    public function appointmentItemFromDto(AppointmentDto $dto): OrderItem
    {
        $order = OrderBuilder::createOrder()
            ->setOrderUid($dto->uid)
            ->setClinicUid($dto->clinicUid)
            ->setEmployeeUid($dto->employeeUid)
            ->setServices($dto->services)
            ->setDateTimeBegin($dto->dateTimeBegin)
            ->setAppointmentDuration($dto->serviceDuration)
            ->setLastName($dto->lastName)
            ->setName($dto->name)
            ->setSecondName($dto->secondName)
            ->setPhone($dto->phone)
            ->setAddress($dto->address ?? '')
            ->setComment($dto->comment ?? '');

        if (is_string($dto->email) && strlen($dto->email) > 0)
        {
            $order->setEmail($dto->email);
        }

        if ($dto->birthday instanceof DateTime)
        {
            $order->setClientBirthday($dto->birthday);
        }

        return $order->build();
    }

    /**
     * @throws \DateMalformedStringException
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
            (string)$data['middleName'],
            (string)$data['email'],
            (string)$data['comment'],
        );
    }

    /**
     * @throws \Exception
     */
    public function waitListItemFromDto(WaitListDto $dto): OrderItem
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

        $waitList = OrderBuilder::createWaitList()
            ->setClinicUid($dto->clinicUid)
            ->setEmployeeUid($dto->employeeUid)
            ->setSpecialtyName($dto->specialtyName)
            ->setDateTimeBegin($dto->dateTimeBegin)
            ->setLastName($dto->lastName)
            ->setName($dto->name)
            ->setSecondName($dto->secondName)
            ->setPhone($dto->phone)
            ->setAddress($dto->address ?? '')
            ->setComment($comment);

        if (is_string($dto->email) && strlen($dto->email) > 0)
        {
            $waitList->setEmail($dto->email);
        }

        return $waitList->build();
    }
}