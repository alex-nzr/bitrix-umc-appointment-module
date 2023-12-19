<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * Bit.Umc - Bitrix integration - SoapClient.php
 * 02.12.2023 03:06
 * ==================================================
 */
namespace ANZ\Appointment\Integration\UmcSdk\Client;

use ANZ\Appointment\Service\Container;
use ANZ\BitUmc\SDK\Core\Dictionary\SoapMethod;
use Exception;
use SimpleXMLElement;

/**
 * @class SoapClient
 * @package ANZ\Appointment\Integration\UmcSdk\Client
 */
class SoapClient extends \ANZ\BitUmc\SDK\Client\SoapClient
{
    /**
     * @param string $method
     * @param \SimpleXMLElement $xml
     * @return array
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Exception
     */
    protected function handleXML(string $method, SimpleXMLElement $xml): array
    {
        $provider = Container::getInstance()->getExchangeDataProvider();
        $result = match ($method) {
            SoapMethod::CLINIC_ACTION_1C->value => $provider->prepareClinicData($xml),
            SoapMethod::EMPLOYEES_ACTION_1C->value => $provider->prepareEmployeesData($xml),
            SoapMethod::NOMENCLATURE_ACTION_1C->value => $provider->prepareNomenclatureData($xml),
            SoapMethod::SCHEDULE_ACTION_1C->value => $provider->prepareScheduleData($xml),
            SoapMethod::CREATE_RESERVE_ACTION_1C->value => $provider->prepareReserveResultData($xml),
            SoapMethod::CREATE_ORDER_ACTION_1C->value,
            SoapMethod::CREATE_WAIT_LIST_ACTION_1C->value,
            SoapMethod::DELETE_ORDER_ACTION_1C->value => $provider->prepareCommonResultData($xml),
            SoapMethod::GET_ORDER_STATUS_ACTION_1C->value => $provider->prepareStatusResultData($xml),

            default => throw new Exception('Can not find way to process xml for method - ' . $method . '.'),
        };

        if ($result->isSuccess())
        {
            return $result->getData();
        }
        else
        {
            throw new Exception(implode('; ', $result->getErrorMessages()));
        }
    }
}