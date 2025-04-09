<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2024
 * ==================================================
 * uclinic.kursk - Exchange.php
 * 03.10.2024 14:31
 * ==================================================
 */
namespace Firstbit\UclinicKursk\Service\OneC;

use ANZ\BitUmc\SDK\Builder;
use ANZ\BitUmc\SDK\Core\Dictionary\ClientScope;
use ANZ\BitUmc\SDK\Core\Dictionary\Protocol;
use ANZ\BitUmc\SDK\Core\Operation\Result;
use ANZ\BitUmc\SDK\Factory;
use ANZ\BitUmc\SDK\Service\Exchange\Http;
use ANZ\BitUmc\SDK\Service\Exchange\Soap;
use ANZ\BitUmc\SDK\Tools\DateFormatter;
use DateTime;
use Exception;
use Firstbit\UclinicKursk\Adapter;
use Firstbit\UclinicKursk\Config\Configuration;
use Firstbit\UclinicKursk\Security\SpamProtector;

/**
 * @class Exchange
 * @package Firstbit\UclinicKursk\Service\OneC
 */
class Exchange
{
    /**
     * @return array[]
     * @throws \Exception
     */
    public static function getAppointmentData(): array
    {
        $exchangeService = static::getSdkExchangeService();

        $result = [
            'clinics' => [],
            'employees' => [],
            'nomenclature' => [],
            'schedule' => []
        ];

        $res = $exchangeService->getClinics();
        if ($res->isSuccess())
        {
            $clinicIds = array_keys($res->getData());
            $result['clinics'] = Adapter\Frontend\ReactMUI::prepareClinicsData($res->getData());
        }
        else
        {
            throw new Exception(implode('; ', $res->getErrorMessages()));
        }

        $res = $exchangeService->getEmployees();
        if ($res->isSuccess())
        {
            $result['employees'] = Adapter\Frontend\ReactMUI::prepareEmployeesData($res->getData());
        }
        else
        {
            throw new Exception(implode('; ', $res->getErrorMessages()));
        }

        foreach ($clinicIds as $clinicUid)
        {
            $res = $exchangeService->getNomenclature($clinicUid);
            if ($res->isSuccess())
            {
                foreach (Adapter\Frontend\ReactMUI::prepareNomenclatureData($res->getData(), $clinicUid) as $nomUid => $nomData)
                {
                    if (key_exists($nomUid, $result['nomenclature']))
                    {
                        $result['nomenclature'][$nomUid]['prices'][$clinicUid] = [
                            "price" => $nomData['price']
                        ];
                    }
                    else
                    {
                        $result['nomenclature'][$nomUid] = $nomData;
                    }
                }
            }
            else
            {
                throw new Exception(implode('; ', $res->getErrorMessages()));
            }
        }

        $res = $exchangeService->getSchedule(Configuration::getDefaultSchedulePeriod());
        if ($res->isSuccess())
        {
            $result['schedule'] = Adapter\Frontend\ReactMUI::prepareScheduleData($res->getData());
        }
        else
        {
            throw new Exception(implode('; ', $res->getErrorMessages()));
        }

        return $result;
    }

    /**
     * @param array $orderData
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     * @throws \Exception
     */
    public static function sendOrder(array $orderData): Result
    {
        $phone = (string)$orderData['phone'];
        if (!empty($phone))
        {
            $count = SpamProtector::getOrdersCountByPhoneToday($phone);
            if ($count >= Configuration::getMaxOrdersByDayForClient())
            {
                throw new Exception('Daily orders limit has been exceeded');
            }
        }

        $exchangeService = static::getSdkExchangeService();
        $reserve = Builder\Order::createReserve()
            ->setClinicUid((string)$orderData['clinicUid'])
            ->setSpecialtyName((string)$orderData["specialty"])
            ->setEmployeeUid((string)$orderData['refUid'])
            ->setDateTimeBegin(new DateTime($orderData['timeBegin']))
            ->build();

        $reserveRes = $exchangeService->sendReserve($reserve);
        if (!$reserveRes->isSuccess())
        {
            throw new Exception(implode('; ', $reserveRes->getErrorMessages()));
        }

        $orderUid = (string)$reserveRes->getData()['uid'];
        if (empty($orderUid))
        {
            throw new Exception('Order uid is empty');
        }

        $services = [];
        if (is_array($orderData['services']) && !empty($orderData['services']))
        {
            $services = array_column($orderData['services'], 'uid');
        }

        $isoDuration = DateFormatter::calculateDurationFromInterval((string)$orderData['timeBegin'], (string)$orderData['timeEnd']);
        $secondsDuration = DateFormatter::formatDurationFromIsoToSeconds($isoDuration);
        $order = Builder\Order::createOrder()
            ->setEmployeeUid((string)$orderData['refUid'])
            ->setName((string)$orderData['name'])
            ->setLastName((string)$orderData['surname'])
            ->setSecondName((string)$orderData['middleName'])
            ->setDateTimeBegin(new DateTime($orderData['timeBegin']))
            ->setPhone($phone)
            ->setEmail((string)$orderData['email'])
            ->setAddress('')
            ->setClinicUid((string)$orderData['clinicUid'])
            ->setOrderUid($orderUid)
            ->setComment((string)$orderData['comment'])
            ->setAppointmentDuration($secondsDuration) // Не учитывается если указаны услуги (setServices). Также есть "особенности" апи УМЦ - длительность считается по услугам, только если для услуги указана индивидуальная длительность для конкретного врача. Длительность из карточки услуги не учитывается.
            ->setServices($services)
            ->build();

        $orderRes = $exchangeService->sendOrder($order);
        if (!$orderRes->isSuccess())
        {
            $exchangeService->deleteOrder($orderUid);
            throw new Exception(implode('; ', $orderRes->getErrorMessages()));
        }

        SpamProtector::incrementOrdersCountByPhoneToday($phone);

        return $orderRes->setData([
            'orderUid' => $orderUid,
            'services' => $services
        ]);
    }

    /**
     * @return \ANZ\BitUmc\SDK\Service\Exchange\Http|\ANZ\BitUmc\SDK\Service\Exchange\Soap
     * @throws \Exception
     */
    protected static function getSdkExchangeService(): Http|Soap
    {
        static $service = null;
        if (is_null($service))
        {
            $client = Builder\ExchangeClient::init()
                ->setLogin(Configuration::getOneCLogin())
                ->setPassword(Configuration::getOneCPassword())
                ->setPublicationProtocol(Protocol::HTTP)
                ->setPublicationAddress(Configuration::getOneCBaseAddress())
                ->setBaseName(Configuration::getOneCBaseName())
                ->setScope(ClientScope::WEB_SERVICE)
                ->build();

            $service = (new Factory\Exchange($client))->create();
        }

        return $service;
    }
}