<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * Bit.Umc - Bitrix integration - IReaderService.php
 * 05.03.2023 21:12
 * ==================================================
 */
namespace ANZ\Appointment\Internals\Contract;

use Bitrix\Main\Result;

/**
 * @interface IReaderService
 * @package ANZ\Appointment\Internals\Contract
 */
interface IReaderService
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
}