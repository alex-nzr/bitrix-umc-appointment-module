<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * Bit.Umc - Bitrix integration - Base.php
 * 06.12.2023 20:42
 * ==================================================
 */
namespace ANZ\Appointment\Service\OneC;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Helper\Orm;
use ANZ\Appointment\Internals\Contract\Service\IExchangeService;
use ANZ\Appointment\Service\Container;
use ANZ\Appointment\Tools\Utils;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Result;
use Exception;
use Throwable;

/**
 * @class Base
 * @package ANZ\Appointment\Service\OneC
 */
abstract class Base implements IExchangeService
{
    protected bool $demoMode;

    public function __construct()
    {
        Loc::loadMessages(__FILE__);
        $this->demoMode = Configuration::getInstance()->isDemoModeOn();
    }

    abstract public function getClinicsList(): Result;
    abstract public function getEmployeesList(): Result;
    abstract public function getNomenclatureList(string $clinicGuid): Result;
    abstract public function getSchedule(array $params = []): Result;

    /**
     * @param string $orderUid
     * @return \Bitrix\Main\Result
     */
    public function getOrderStatus(string $orderUid): Result
    {
        try
        {
            if ($this->demoMode)
            {
                throw new Exception('Can not use this request when DemoMode is ON');
            }

            $sdkResult = Container::getInstance()->getSdkExchangeService()->getOrderStatus($orderUid);
            return Utils::convertSdkResultToBitrixResult($sdkResult);
        }
        catch (Throwable $e)
        {
            return (new Result)->addError(new Error($e->getMessage()));
        }
    }

    /**
     * @param array $params
     * @return \Bitrix\Main\Result
     */
    public function addOrder(array $params): Result
    {
        try
        {
            if ($this->demoMode){
                sleep(3);
                return (new Result())->setData(['success' => true]);
            }

            $orderUid = $this->getReserveUid($params);

            if (strlen($orderUid) <= 0)
            {
                throw new Exception(Loc::getMessage("ANZ_APPOINTMENT_RESERVE_ERROR"));
            }
            else
            {
                $params['orderUid'] = $orderUid;
            }

            $order = Container::getInstance()->getOrderConverter()->orderFromArray($params);
            $res = Container::getInstance()->getSdkExchangeService()->sendOrder($order);
            if ($res->isSuccess())
            {
                $ormRes = Orm::addRecord($params);
                if (!$ormRes->isSuccess())
                {
                    throw new Exception(implode('; ', $ormRes->getErrorMessages()));
                }
            }
            else
            {
                $this->deleteOrder($orderUid);
            }

            return Utils::convertSdkResultToBitrixResult($res);
        }
        catch (Throwable $e)
        {
            return (new Result)->addError(new Error($e->getMessage()));
        }
    }

    /**
     * @param array $params
     * @return string
     * @throws \Exception|\Psr\Container\NotFoundExceptionInterface
     */
    public function getReserveUid(array $params): string
    {
        $reserve = Container::getInstance()->getOrderConverter()->reserveFromArray($params);
        $sdkResult = Container::getInstance()->getSdkExchangeService()->sendReserve($reserve);
        $res = Utils::convertSdkResultToBitrixResult($sdkResult);
        if ($res->isSuccess()){
            $data = $res->getData();
            return !empty($data['XML_ID']) ? $data['XML_ID'] : "";
        }
        else
        {
            throw new Exception(implode('; ', $res->getErrorMessages()));
        }
    }

    /**
     * @param array $params
     * @return \Bitrix\Main\Result
     */
    public function addWaitingList(array $params): Result
    {
        try
        {
            if ($this->demoMode){
                sleep(3);
                return (new Result)->setData(['success' => true]);
            }

            $waitList = Container::getInstance()->getOrderConverter()->waitListFromArray($params);
            $sdkResult = Container::getInstance()->getSdkExchangeService()->sendReserve($waitList);
            return Utils::convertSdkResultToBitrixResult($sdkResult);
        }
        catch (Throwable $e)
        {
            return (new Result())->addError(new Error($e->getMessage()));
        }
    }

    /**
     * @param string $orderUid
     * @return \Bitrix\Main\Result
     */
    public function deleteOrder(string $orderUid): Result
    {
        try
        {
            if ($this->demoMode){
                throw new Exception('Can not use this request when DemoMode is ON');
            }
            $sdkResult = Container::getInstance()->getSdkExchangeService()->deleteOrder($orderUid);
            return Utils::convertSdkResultToBitrixResult($sdkResult);
        }
        catch (Throwable $e)
        {
            return (new Result)->addError(new Error($e->getMessage()));
        }
    }
}