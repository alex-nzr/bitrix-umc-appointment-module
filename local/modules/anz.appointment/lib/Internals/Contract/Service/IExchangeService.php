<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * Bit.Umc - Bitrix integration - IExchangeService.php
 * 05.03.2023 21:12
 * ==================================================
 */
namespace ANZ\Appointment\Internals\Contract\Service;

use Bitrix\Main\Result;

/**
 * @interface IExchangeService
 * @package ANZ\Appointment\Internals\Contract\Service
 */
interface IExchangeService
{
    /**
     * @return \Bitrix\Main\Result
     */
    public function getClinicsList(): Result;

    /**
     * @return \Bitrix\Main\Result
     */
    public function getEmployeesList(): Result;

    /**
     * @param string $clinicGuid
     * @return \Bitrix\Main\Result
     */
    public function getNomenclatureList(string $clinicGuid): Result;

    /**
     * @param array $params
     * @return \Bitrix\Main\Result
     */
    public function getSchedule(array $params = []): Result;

    /**
     * @param string $orderUid
     * @return \Bitrix\Main\Result
     */
    public function getOrderStatus(string $orderUid): Result;

    /**
     * @param array $params
     * @return \Bitrix\Main\Result
     */
    public function addOrder(array $params): Result;

    /**
     * @param array $params
     * @return string
     */
    public function getReserveUid(array $params): string;

    /**
     * @param array $params
     * @return \Bitrix\Main\Result
     */
    public function addWaitingList(array $params): Result;

    /**
     * @param string $orderUid
     * @return \Bitrix\Main\Result
     */
    public function deleteOrder(string $orderUid): Result;
}