<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * Bit.Umc - Bitrix integration - Order.php
 * 06.12.2023 16:06
 * ==================================================
 */
namespace ANZ\Appointment\Service\Converter;

use ANZ\Appointment\Integration\UmcSdk\Builder\Order as OrderBuilder;
use ANZ\BitUmc\SDK\Tools\PhoneFormatter;
use Bitrix\Main\Localization\Loc;
use DateTime as PhpDateTime;

/**
 * @class Order
 * @package ANZ\Appointment\Service\Converter
 */
class Order
{
    public function __construct()
    {
        Loc::loadMessages(__FILE__);
    }

    /**
     * @param array $params
     * @return \ANZ\BitUmc\SDK\Item\Order
     * @throws \Exception
     */
    public function reserveFromArray(array $params): \ANZ\BitUmc\SDK\Item\Order
    {
        return OrderBuilder::createReserve()
            ->setClinicUid((string)$params['clinicUid'])
            ->setSpecialtyName((string)$params['specialty'])
            ->setEmployeeUid((string)$params['employeeUid'])
            ->setDateTimeBegin(new PhpDateTime((string)$params['timeBegin']))
            ->build();
    }

    /**
     * @throws \Exception
     */
    public function orderFromArray(array $params): \ANZ\Appointment\Integration\UmcSdk\Item\Order
    {
        $order = OrderBuilder::createOrder()
            ->setEmployeeUid((string)$params['employeeUid'])
            ->setName((string)$params['name'])
            ->setLastName((string)$params['surname'])
            ->setSecondName((string)$params['middleName'])
            ->setDateTimeBegin(new PhpDateTime((string)$params['timeBegin']))
            ->setPhone((string)$params['phone'])
            ->setEmail((string)$params['email'])
            ->setAddress(key_exists('address', $params) ? (string)$params['address'] : '')
            ->setClinicUid((string)$params['clinicUid'])
            ->setOrderUid(key_exists('orderUid', $params) ? (string)$params['orderUid'] : '')
            ->setComment((string)$params['comment']);

        if (key_exists('customParams', $params) && is_array($params['customParams']))
        {
            $order->setCustomParams($params['customParams']);
        }

        if (key_exists('birthday', $params) && !empty($params['birthday']))
        {
            $order->setClientBirthday(new PhpDateTime((string)$params['birthday']));
        }

        if ((int)$params['serviceDuration'] > 0)
        {
            $order->setAppointmentDuration((int)$params["serviceDuration"]);
        }
        elseif(!empty($params['timeBegin']) && !empty($params['timeEnd']))
        {
            $startDate = new PhpDateTime($params['timeBegin']);
            $diff = $startDate->diff(new PhpDateTime($params['timeEnd']));
            $order->setAppointmentDuration($diff->s);
        }

        if (!empty($params['serviceUid']))
        {
            $order->setServices([$params['serviceUid']]);
        }

        return $order->build();
    }

    /**
     * @param array $params
     * @return \ANZ\BitUmc\SDK\Item\Order
     * @throws \Exception
     */
    public function waitListFromArray(array $params): \ANZ\BitUmc\SDK\Item\Order
    {
        $comment = Loc::getMessage('ANZ_APPOINTMENT_WAITING_LIST_COMMENT', [
            '#FULL_NAME#' => $params['name'] ." ". $params['middleName'] ." ". $params['surname'],
            '#PHONE#'     => PhoneFormatter::formatPhone($params['phone']),
            '#DATE#'      => date("d.m.Y", strtotime($params['orderDate'])),
            '#TIME#'      => date("H:i", strtotime($params['timeBegin'])),
            '#COMMENT#'   => $params['comment'] ?? '',
        ]);

        return OrderBuilder::createWaitList()
            ->setSpecialtyName($params['specialty'] ?? "")
            ->setName((string)$params['name'])
            ->setLastName((string)$params['surname'])
            ->setSecondName((string)$params['middleName'])
            ->setDateTimeBegin(new PhpDateTime((string)$params['timeBegin']))
            ->setPhone((string)$params['phone'])
            ->setEmail((string)$params['email'])
            ->setAddress(key_exists('address', $params) ? (string)$params['address'] : '')
            ->setClinicUid((string)$params['clinicUid'])
            ->setComment((string)$comment)
            ->build();
    }
}