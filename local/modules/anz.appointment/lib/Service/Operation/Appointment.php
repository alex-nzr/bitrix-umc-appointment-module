<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - Appointment.php
 * 10.07.2022 22:37
 * ==================================================
 */
namespace ANZ\Appointment\Service\Operation;

use ANZ\Appointment\Helper\Orm;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Exception;
use ANZ\Appointment\Config\Constants;
use ANZ\Appointment\Event\Event;
use ANZ\Appointment\Event\EventType;
use ANZ\Appointment\Service\Container;

/**
 * Class Appointment
 * @package ANZ\Appointment\Service\Operation
 */
class Appointment
{
    /**
     * @param array $arParams
     * @return \Bitrix\Main\Result
     */
    public static function addOrder(array $arParams): Result
    {
        try
        {
            $container = Container::getInstance();
            $writer = $container->getWriterService();

            $useWaitingList = Option::get(
                Constants::APPOINTMENT_MODULE_ID,
                'appointment_settings_use_waiting_list', "N"
            );

            if ($useWaitingList === "Y")
            {
                $response = $writer->addWaitingList($arParams);
            }
            else
            {
                $arParams = Event::getEventHandlersResult(EventType::ON_BEFORE_ORDER_SEND, $arParams);
                $response = $writer->addOrder($arParams);
            }

            return $response;
        }
        catch (Exception $e)
        {
            return (new Result)->addError(new Error($e->getMessage()));
        }
    }

    /**
     * @param int $id
     * @param string $orderUid
     * @return \Bitrix\Main\Result
     */
    public static function deleteOrder(int $id, string $orderUid): Result
    {
        try
        {
            $container = Container::getInstance();
            $writer = $container->getWriterService();

            $response = $writer->deleteOrder($orderUid);
            if ($response->isSuccess())
            {
                $ormRes = Orm::deleteRecord($id);
                $data = $response->getData();
                $response->setData(array_merge($data, $ormRes));
                return $response;
            }
            else
            {
                throw new Exception(implode(", ", $response->getErrorMessages()));
            }
        }
        catch (Exception $e)
        {
            return (new Result)->addError(new Error($e->getMessage()));
        }
    }

    /**
     * @param int $id
     * @param string $orderUid
     * @return \Bitrix\Main\Result
     */
    public static function getOrderStatus(int $id, string $orderUid): Result
    {
        try
        {
            $container = Container::getInstance();
            $reader = $container->getReaderService();

            $response = $reader->getOrderStatus($orderUid);
            if ($response->isSuccess())
            {
                $data = $response->getData();
                $status = $data['status'] ?? "-";
                $ormRes = Orm::updateRecord($id, ['STATUS_1C' => $status]);
                $response->setData(array_merge($data, $ormRes->getData()));
                return $response;
            }
            else
            {
                throw new Exception(implode(", ", $response->getErrorMessages()));
            }
        }
        catch (Exception $e)
        {
            return (new Result)->addError(new Error($e->getMessage()));
        }
    }
}