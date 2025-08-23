<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.07.2022
 * ==================================================
*/
namespace ANZ\Appointment\Service\Operation;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Config\Constants;
use ANZ\Appointment\Event\Event;
use ANZ\Appointment\Event\EventType;
use ANZ\Appointment\Service\Container;
use ANZ\Appointment\Tools\Orm;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Exception;
use Throwable;

/**
 * Class Appointment
 * @package ANZ\Appointment\Service\Operation
 */
class Appointment
{
    /**
     * @param array $arParams
     * @return \Bitrix\Main\Result
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function addOrder(array $arParams): Result
    {
        try
        {
            $container = Container::getInstance();
            $writer = $container->getExchangeService();

            $useWaitingList = Option::get(
                Configuration::getModuleId(),
                Constants::OPTION_KEY_USE_WAIT_LIST, "N"
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
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function deleteOrder(int $id, string $orderUid): Result
    {
        try
        {
            $container = Container::getInstance();
            $writer = $container->getExchangeService();

            $response = $writer->deleteOrder($orderUid);
            if ($response->isSuccess())
            {
                $ormRes = Orm::deleteRecord($id);
                $data = $response->getData();
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

    /**
     * @param int $id
     * @param string $orderUid
     * @return \Bitrix\Main\Result
     */
    public static function getOrderStatus(int $id, string $orderUid): Result
    {
        try
        {
            $response = Container::getInstance()->getExchangeService()->getOrderStatus($orderUid);
            if ($response->isSuccess())
            {
                $data = $response->getData();
                $status = $data['statusTitle'] ?? "-";
                $ormRes = Orm::updateRecord($id, ['STATUS_1C' => $status]);
                $response->setData(array_merge($data, $ormRes->getData()));
                return $response;
            }
            else
            {
                throw new Exception(implode(", ", $response->getErrorMessages()));
            }
        }
        catch (Throwable $e)
        {
            return (new Result)->addError(new Error($e->getMessage()));
        }
    }
}