<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.07.2022
 * ==================================================
*/
namespace ANZ\Appointment\Integration\UmcSdk\Gateway\Client;

use ANZ\Appointment\Integration\UmcSdk\Provider\ExchangeDataProvider;
use ANZ\BitUmc\SDK\Core\Dictionary\ClientScope;
use ANZ\BitUmc\SDK\Core\Dictionary\Protocol;
use ANZ\BitUmc\SDK\Core\Dictionary\SoapMethod;
use Exception;
use SimpleXMLElement;

class SoapClient extends \ANZ\BitUmc\SDK\Client\SoapClient
{
    private static ?ExchangeDataProvider $provider = null;

    public static function create(
        string $login,
        string $password,
        Protocol $protocol,
        string $address,
        string $baseName,
        ClientScope $scope,
        ExchangeDataProvider $provider = null
    ): static
    {
        if (is_null($provider))
        {
            throw new Exception('Data provider is required to use ' . static::class);
        }
        static::$provider = $provider;
        return parent::create($login, $password, $protocol, $address, $baseName, $scope);
    }

    /**
     * @throws \Exception
     */
    protected function handleXML(string $method, SimpleXMLElement $xml): array
    {
        $result = match ($method) {
            SoapMethod::CLINIC_ACTION_1C->value => static::$provider->prepareClinicData($xml),
            SoapMethod::EMPLOYEES_ACTION_1C->value => static::$provider->prepareEmployeesData($xml),
            SoapMethod::NOMENCLATURE_ACTION_1C->value => static::$provider->prepareNomenclatureData($xml),
            SoapMethod::SCHEDULE_ACTION_1C->value => static::$provider->prepareScheduleData($xml),
            SoapMethod::CREATE_RESERVE_ACTION_1C->value => static::$provider->prepareReserveResultData($xml),
            SoapMethod::CREATE_ORDER_ACTION_1C->value,
            SoapMethod::CREATE_WAIT_LIST_ACTION_1C->value,
            SoapMethod::DELETE_ORDER_ACTION_1C->value => static::$provider->prepareCommonResultData($xml),
            SoapMethod::GET_ORDER_STATUS_ACTION_1C->value => static::$provider->prepareStatusResultData($xml),

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