<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - ExchangeDataProvider.php
 * 06.12.2023 12:37
 * ==================================================
 */
namespace ANZ\Appointment\Service\Provider;

use ANZ\Appointment\Service\Container;
use ANZ\BitUmc\SDK\Service\XmlParser;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Result;
use Exception;
use ANZ\Appointment\Event\Event;
use ANZ\Appointment\Event\EventType;
use SimpleXMLElement;

/**
 * @class ExchangeDataProvider
 * @package ANZ\Appointment\Service\Provider
 */
class ExchangeDataProvider
{
    protected array $fieldMap;
    protected XmlParser $sdkXmlParser;

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct()
    {
        Loc::loadMessages(__FILE__);
        $this->sdkXmlParser = Container::getInstance()->getXmlParser();
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return \Bitrix\Main\Result
     */
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

    /**
     * @param \SimpleXMLElement $xml
     * @return \Bitrix\Main\Result
     */
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

    /**
     * @param \SimpleXMLElement $xml
     * @return \Bitrix\Main\Result
     */
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

    /**
     * @param \SimpleXMLElement $xml
     * @return \Bitrix\Main\Result
     */
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
     * Parse result for add order, delete order and add wait list requests
     * @param \SimpleXMLElement $xml
     * @return \Bitrix\Main\Result
     * @throws \Exception
     */
    public function prepareCommonResultData(SimpleXMLElement $xml): Result
    {
        return (new Result())->setData($this->sdkXmlParser->prepareCommonResultData($xml));
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return \Bitrix\Main\Result
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
     * @param \SimpleXMLElement $xml
     * @return \Bitrix\Main\Result
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