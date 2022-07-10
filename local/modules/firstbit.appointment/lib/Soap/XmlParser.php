<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - XmlParser.php
 * 10.07.2022 22:37
 * ==================================================
 */
namespace FirstBit\Appointment\Soap;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Exception;
use FirstBit\Appointment\Config\Constants;
use FirstBit\Appointment\Event\Event;
use FirstBit\Appointment\Event\EventType;
use FirstBit\Appointment\Tools\Utils;
use SimpleXMLElement;

/**
 * Class XmlParser
 * @package FirstBit\Appointment\Soap
 */
class XmlParser{

    public function __construct(){}

    /**
     * @param \SimpleXMLElement $xml
     * @return \Bitrix\Main\Result
     */
    public function prepareClinicData(SimpleXMLElement $xml): Result
    {
        $result = new Result();
        try
        {
            $xmlArr = $this->xmlToArray($xml);
            $xmlArr = Event::getEventHandlersResult(EventType::ON_BEFORE_CLINICS_PARSED, $xmlArr);

            $clinics = [];
            if (is_array($xmlArr['Клиника']))
            {
                if (Utils::is_assoc($xmlArr['Клиника']))
                {
                    $clinics[$xmlArr['Клиника']['УИД']] = [
                        'uid' => $xmlArr['Клиника']['УИД'],
                        'name' => $xmlArr['Клиника']['Наименование']
                    ];
                }
                else
                {
                    foreach ($xmlArr['Клиника'] as $item) {
                        $clinic = [];
                        $clinic['uid'] = $item['УИД'];
                        $clinic['name'] = $item['Наименование'];
                        $clinics[$item['УИД']] = $clinic;
                    }
                }
            }
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
            $xmlArr = $this->xmlToArray($xml);
            $xmlArr = Event::getEventHandlersResult(EventType::ON_BEFORE_EMPLOYEES_PARSED, $xmlArr);
            $employees = [];
            if (is_array($xmlArr['Сотрудник']))
            {
                foreach ($xmlArr['Сотрудник'] as $item)
                {
                    $employee = [];
                    $clinicUid = ($item['Организация'] == "00000000-0000-0000-0000-000000000000") ? "" : $item['Организация'];
                    $uid = is_array($item['UID']) ? current($item['UID']) : $item['UID'];

                    $employee['uid']          = $uid;
                    $employee['name']         = $item['Имя'];
                    $employee['surname']      = $item['Фамилия'];
                    $employee['middleName']   = $item['Отчество'];
                    $employee['fullName']     = $item['Фамилия'] ." ". $item['Имя'] ." ". $item['Отчество'];
                    $employee['clinicUid']    = $clinicUid;
                    $employee['photo']        = $item['Фото'];
                    $employee['description']  = $item['КраткоеОписание'];
                    $employee['specialty']    = $item['Специализация'];
                    $employee['specialtyUid'] = base64_encode($item['Специализация']);
                    $employee['services']     = [];

                    if (is_array($item['ОсновныеУслуги']['ОсновнаяУслуга']))
                    {
                        foreach ($item['ОсновныеУслуги']['ОсновнаяУслуга'] as $service)
                        {
                            if (!empty($service['UID']))
                            {
                                $employee['services'][$service['UID']] = [
                                    'title'            => 'deprecated field',
                                    'personalDuration' => strtotime($service['Продолжительность'])-strtotime('0001-01-01T00:00:00')
                                ];
                            }
                        }
                    }

                    $employees[$uid] = $employee;
                }
            }
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
            $xmlArr = $this->xmlToArray($xml);
            $xmlArr = Event::getEventHandlersResult(EventType::ON_BEFORE_NOMENCLATURE_PARSED, $xmlArr);
            $nomenclature = [];
            if (is_array($xmlArr['Каталог']))
            {
                foreach ($xmlArr['Каталог'] as $item)
                {
                    if ($item['ЭтоПапка'] === true){
                        continue;
                    }
                    $uid = is_array($item['UID']) ? current($item['UID']) : $item['UID'];

                    $product = [];
                    $product['uid']         = $uid;
                    $product['name']        = $item['Наименование'];
                    $product['typeOfItem']  = $item['Вид'];
                    $product['artNumber']   = $item['Артикул'];
                    $product['price']       = str_replace("[^0-9]", '', $item['Цена']);
                    $product['duration']    = Utils::formatDurationToSeconds($item['Продолжительность']);
                    $nomenclature[$uid]     = $product;
                }
            }
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
            $xmlArr = $this->xmlToArray($xml);
            $xmlArr = Event::getEventHandlersResult(EventType::ON_BEFORE_SCHEDULE_PARSED, $xmlArr);
            $schedule = [];
            if (is_array($xmlArr['ГрафикДляСайта'])){
                $schedule = $this->processScheduleData($xmlArr['ГрафикДляСайта']);
            }
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
     * prepare schedule data for frontend
     * @param array $schedule
     * @return array
     */
    public function processScheduleData(array $schedule): array
    {
        if (Utils::is_assoc($schedule))
        {
            $schedule = array($schedule);
        }

        $formattedSchedule = [];
        foreach ($schedule as $key => $item)
        {
            if (isset($item["СотрудникID"])){
                $formattedSchedule[$key]["refUid"] = $item["СотрудникID"];
            }
            if (isset($item["Специализация"])){
                $formattedSchedule[$key]["specialty"] = $item["Специализация"];
            }
            if (isset($item["СотрудникФИО"])){
                $formattedSchedule[$key]["name"] = $item["СотрудникФИО"];
            }
            if (isset($item["Клиника"])){
                $formattedSchedule[$key]["clinicUid"] = $item["Клиника"];
            }

            $duration = 0;
            if (isset($item["ДлительностьПриема"])){
                $formattedSchedule[$key]["duration"] = $item["ДлительностьПриема"];
                $duration = intval(date("H", strtotime($item["ДлительностьПриема"]))) * 3600
                    + intval(date("i", strtotime($item["ДлительностьПриема"]))) * 60;
                $formattedSchedule[$key]["durationInSeconds"] = $duration;
            }

            $freeTime = (is_array($item["ПериодыГрафика"]["СвободноеВремя"]) && count($item["ПериодыГрафика"]["СвободноеВремя"]) > 0)
                ? $item["ПериодыГрафика"]["СвободноеВремя"]["ПериодГрафика"] : [];
            $busyTime = (is_array($item["ПериодыГрафика"]["ЗанятоеВремя"]) && count($item["ПериодыГрафика"]["ЗанятоеВремя"]) > 0)
                ? $item["ПериодыГрафика"]["ЗанятоеВремя"]["ПериодГрафика"] : [];

            if (Utils::is_assoc($freeTime)) {
                $freeTime = array($freeTime);
            }
            if (Utils::is_assoc($busyTime)) {
                $busyTime = array($busyTime);
            }

            $formattedSchedule[$key]["timetable"]["free"] = $this->formatTimetable($freeTime, $duration);
            $formattedSchedule[$key]["timetable"]["busy"] = $this->formatTimetable($busyTime, 0, true);
            $formattedSchedule[$key]["timetable"]["freeNotFormatted"] = $this->formatTimetable($freeTime, 0, true);
        }
        return [
            "schedule" => $formattedSchedule,
        ];
    }

    /** Beautify array of timelines
     * @param $array
     * @param int $duration
     * @param bool $useDefaultInterval
     * @return array
     */
    public function formatTimetable($array, int $duration, $useDefaultInterval = false): array
    {
        if (!is_array($array) || empty($array)){
            return [];
        }

        if (!$duration > 0){
            $duration = Option::get(
                Constants::APPOINTMENT_MODULE_ID,
                'appointment_settings_default_duration',
                Constants::DEFAULT_APPOINTMENT_DURATION_SEC
            );
        }

        if (!empty($array))
        {
            if (Utils::is_assoc($array)) {
                $array = array($array);
            }
            $formattedArray = [];
            foreach ($array as $item)
            {
                $timestampTimeBegin = strtotime($item["ВремяНачала"]);
                $timestampTimeEnd = strtotime($item["ВремяОкончания"]);

                if ($useDefaultInterval)
                {
                    $formattedArray[] = $this->formatTimeTableItem($item, (int)$timestampTimeBegin, (int)$timestampTimeEnd);
                }
                else
                {
                    $timeDifference = $timestampTimeEnd - $timestampTimeBegin;
                    $appointmentsCount = round($timeDifference / $duration);

                    for ($i = 0; $i < $appointmentsCount; $i++)
                    {
                        $start = $timestampTimeBegin + ($duration * $i);
                        $end = $timestampTimeBegin + ($duration * ($i+1));

                        $formattedArray[] = $this->formatTimeTableItem($item, (int)$start, (int)$end);
                    }
                }
            }
            return $formattedArray;
        }
        else
        {
            return [];
        }
    }

    /**
     * @param array $item
     * @param int $start
     * @param int $end
     * @return array
     */
    public function formatTimeTableItem(array $item, int $start, int $end): array
    {
        return [
            "typeOfTimeUid" => $item["ВидВремени"],
            "date" => $item["Дата"],
            "timeBegin" => date("Y-m-d", $start) ."T". date("H:i:s", $start),
            "timeEnd" => date("Y-m-d", $end) ."T". date("H:i:s", $end),
            "formattedDate" => date("d-m-Y", strtotime($item["Дата"])),
            "formattedTimeBegin" => date("H:i", $start),
            "formattedTimeEnd" => date("H:i", $end),
        ];
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return \Bitrix\Main\Result
     */
    public function prepareOrderResultData(SimpleXMLElement $xml): Result
    {
        $result = new Result();
        $xmlArr = $this->xmlToArray($xml);
        if ($xmlArr["Результат"] === "true"){
            $result->setData(['success' => true]);
        }
        else {
            $result->addError(new Error((string)$xmlArr["ОписаниеОшибки"]));
        }
        return $result;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return \Bitrix\Main\Result
     */
    public function prepareReserveResultData(SimpleXMLElement $xml): Result
    {
        $result = new Result();
        $xmlArr = $this->xmlToArray($xml);
        if ($xmlArr["Результат"] === "true" && !empty($xmlArr["УИД"])){
            $result->setData([
                'success' => true,
                'XML_ID'  => $xmlArr["УИД"]
            ]);
        }
        else {
            $result->addError(new Error((string)$xmlArr["ОписаниеОшибки"]));
        }
        return $result;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return \Bitrix\Main\Result
     */
    public function prepareWaitListResultData(SimpleXMLElement $xml): Result
    {
        $result = new Result();
        $xmlArr = $this->xmlToArray($xml);
        if ($xmlArr["Результат"] === "true"){
            $result->setData(['success' => true]);
        }
        else {
            $result->addError(new Error((string)$xmlArr["ОписаниеОшибки"]));
        }
        return $result;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return \Bitrix\Main\Result
     */
    public function prepareDeleteResultData(SimpleXMLElement $xml): Result
    {
        $result = new Result();
        $xmlArr = $this->xmlToArray($xml);
        if ($xmlArr["Результат"] === "true"){
            $result->setData(['success' => true]);
        }
        else {
            $result->addError(new Error((string)$xmlArr["ОписаниеОшибки"]));
        }
        return $result;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return \Bitrix\Main\Result
     */
    public function prepareStatusResultData(SimpleXMLElement $xml): Result
    {
        $result = new Result();
        $xmlArr = $this->xmlToArray($xml);
        if ((int)$xmlArr["Результат"] > 0)
        {
            $result->setData([
                'success'   => true,
                'statusId'  => $xmlArr['Результат'],
                'status'    => $xmlArr['ОписаниеРезультата']
            ]);
        }
        else {
            $result->addError(new Error($xmlArr["Результат"] ." - ". $xmlArr["ОписаниеОшибки"]));
        }
        return $result;
    }

    /**
     * @param SimpleXMLElement $xml
     * @return array
     */
    public function xmlToArray(SimpleXMLElement $xml): array
    {
        return json_decode(json_encode($xml), true);
    }
}