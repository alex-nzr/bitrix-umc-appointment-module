<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 05.03.2023
 * ==================================================
*/
namespace ANZ\Appointment\Internals\Contract\Service;

use Bitrix\Main\Result;

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
     * @param string $clinicUid
     * @return \Bitrix\Main\Result
     */
    public function getNomenclatureList(string $clinicUid): Result;

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