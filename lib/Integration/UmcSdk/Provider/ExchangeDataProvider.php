<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.07.2022
 * ==================================================
*/
namespace ANZ\Appointment\Integration\UmcSdk\Provider;

use ANZ\Appointment\Event\Event;
use ANZ\Appointment\Event\EventType;
use ANZ\BitUmc\SDK\Service\XmlParser;
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Exception;
use SimpleXMLElement;

class ExchangeDataProvider
{
    public function __construct(protected XmlParser $sdkXmlParser)
    {
    }

    public function prepareClinicData(SimpleXMLElement $xml): Result
    {
        $result = new Result();
        try
        {
            Event::getEventHandlersResult(EventType::ON_BEFORE_CLINICS_PARSED, $this->sdkXmlParser->xmlToArray($xml));
            $clinics = $this->sdkXmlParser->prepareClinicData($xml);
            $result->setData(
                (array)Event::getEventHandlersResult(EventType::ON_AFTER_CLINICS_PARSED, $clinics)
            );
        }
        catch (Exception $e)
        {
            $result->addError(new Error($e->getMessage()));
        }
        return $result;
    }

    public function prepareEmployeesData(SimpleXMLElement $xml): Result
    {
        $result = new Result();
        try
        {
            Event::getEventHandlersResult(
                EventType::ON_BEFORE_EMPLOYEES_PARSED,
                $this->sdkXmlParser->xmlToArray($xml)
            );
            $employees = $this->sdkXmlParser->prepareEmployeesData($xml);
            $result->setData(
                (array)Event::getEventHandlersResult(EventType::ON_AFTER_EMPLOYEES_PARSED, $employees)
            );
        }
        catch (Exception $e)
        {
            $result->addError(new Error($e->getMessage()));
        }
        return $result;
    }

    public function prepareNomenclatureData(SimpleXMLElement $xml): Result
    {
        $result = new Result();
        try
        {
            Event::getEventHandlersResult(
                EventType::ON_BEFORE_NOMENCLATURE_PARSED,
                $this->sdkXmlParser->xmlToArray($xml)
            );

            $nomenclature = $this->sdkXmlParser->prepareNomenclatureData($xml);
            $result->setData(
                (array)Event::getEventHandlersResult(EventType::ON_AFTER_NOMENCLATURE_PARSED, $nomenclature)
            );
        }
        catch (Exception $e)
        {
            $result->addError(new Error($e->getMessage()));
        }
        return $result;
    }

    public function prepareScheduleData(SimpleXMLElement $xml): Result
    {
        $result = new Result();
        try
        {
            Event::getEventHandlersResult(EventType::ON_BEFORE_SCHEDULE_PARSED, $this->sdkXmlParser->xmlToArray($xml));

            $schedule = $this->sdkXmlParser->prepareScheduleData($xml);
            $result->setData(
                (array)Event::getEventHandlersResult(EventType::ON_AFTER_SCHEDULE_PARSED, $schedule)
            );
        }
        catch (Exception $e)
        {
            $result->addError(new Error($e->getMessage()));
        }
        return $result;
    }

    /**
     * @throws \Exception
     */
    public function prepareCommonResultData(SimpleXMLElement $xml): Result
    {
        return (new Result())->setData($this->sdkXmlParser->prepareCommonResultData($xml));
    }

    /**
     * @throws \Exception
     */
    public function prepareReserveResultData(SimpleXMLElement $xml): Result
    {
        $result = new Result();
        $data = $this->sdkXmlParser->prepareReserveResultData($xml);

        if (key_exists('uid', $data) && !empty($data['uid']))
        {
            $result->setData([
                'success' => true,
                'XML_ID'  => $data['uid']
            ]);
        }
        else
        {
            $result->addError(new Error('Something went wrong. Response - ' . json_encode($data)));
        }
        return $result;
    }

    /**
     * @throws \Exception
     */
    public function prepareStatusResultData(SimpleXMLElement $xml): Result
    {
        $result = new Result();
        $data = $this->sdkXmlParser->prepareStatusResultData($xml);

        if (key_exists('statusId', $data) && key_exists('statusTitle', $data))
        {
            $result->setData(array_merge(['success' => true], $data));
        }
        else
        {
            $result->addError(new Error('Something went wrong. Response - ' . json_encode($data)));
        }
        return $result;
    }
}