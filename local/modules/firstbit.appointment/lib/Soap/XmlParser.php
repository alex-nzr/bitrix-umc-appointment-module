<?php
namespace FirstBit\Appointment\Soap;

use Exception;
use FirstBit\Appointment\Event\Event;
use FirstBit\Appointment\Event\EventType;
use FirstBit\Appointment\Utils\Utils;
use SimpleXMLElement;

class XmlParser{

    public function __construct(){}

    /**
     * @param \SimpleXMLElement $xml
     * @return array
     */
    public function prepareClinicData(SimpleXMLElement $xml): array
    {
        try
        {
            $xmlArr = $this->xmlToArray($xml);
            $xmlArr = Event::getEventHandlersResult(EventType::ON_BEFORE_CLINICS_PARSED, $xmlArr);
            $clinics = [];
            if (is_array($xmlArr['Клиника'])){
                foreach ($xmlArr['Клиника'] as $item) {
                    $clinic = [];
                    $clinic['uid'] = $item['УИД'];
                    $clinic['name'] = $item['Наименование'];
                    $clinics[$item['УИД']] = $clinic;
                }
            }
            return Event::getEventHandlersResult(EventType::ON_AFTER_CLINICS_PARSED, $clinics);
        }
        catch (Exception $e)
        {
            return Utils::createErrorArray($e->getMessage());
        }
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return array
     */
    public function prepareEmployeesData(SimpleXMLElement $xml): array
    {
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
            return Event::getEventHandlersResult(EventType::ON_AFTER_EMPLOYEES_PARSED, $employees);
        }
        catch (Exception $e)
        {
            return Utils::createErrorArray($e->getMessage());
        }
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return array
     */
    public function prepareNomenclatureData(SimpleXMLElement $xml): array
    {
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
            return Event::getEventHandlersResult(EventType::ON_AFTER_NOMENCLATURE_PARSED, $nomenclature);
        }
        catch (Exception $e)
        {
            return Utils::createErrorArray($e->getMessage());
        }
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return array
     */
    public function prepareScheduleData(SimpleXMLElement $xml): array
    {
        try
        {
            $xmlArr = $this->xmlToArray($xml);
            $xmlArr = Event::getEventHandlersResult(EventType::ON_BEFORE_SCHEDULE_PARSED, $xmlArr);
            $schedule = [];
            if (is_array($xmlArr['ГрафикДляСайта'])){
                $schedule = Utils::prepareScheduleData($xmlArr['ГрафикДляСайта']);
            }
            return Event::getEventHandlersResult(EventType::ON_AFTER_SCHEDULE_PARSED, $schedule);
        }
        catch (Exception $e)
        {
            return Utils::createErrorArray($e->getMessage());
        }
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return array
     */
    public function prepareOrderResultData(SimpleXMLElement $xml): array
    {
        $xmlArr = $this->xmlToArray($xml);
        if ($xmlArr["Результат"] === "true"){
            return ['success' => true];
        }
        else {
            return Utils::createErrorArray($xmlArr["ОписаниеОшибки"]);
        }
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return array
     */
    public function prepareReserveResultData(SimpleXMLElement $xml): array
    {
        $xmlArr = $this->xmlToArray($xml);
        if ($xmlArr["Результат"] === "true" && !empty($xmlArr["УИД"])){
            return [
                'success' => true,
                'XML_ID'  => $xmlArr["УИД"]
            ];
        }
        else {
            return Utils::createErrorArray($xmlArr["ОписаниеОшибки"]);
        }
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return array
     */
    public function prepareWaitListResultData(SimpleXMLElement $xml): array
    {
        $xmlArr = $this->xmlToArray($xml);
        if ($xmlArr["Результат"] === "true"){
            return ['success' => true];
        }
        else {
            return Utils::createErrorArray($xmlArr["ОписаниеОшибки"]);
        }
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return array
     */
    public function prepareDeleteResultData(SimpleXMLElement $xml): array
    {
        $xmlArr = $this->xmlToArray($xml);
        if ($xmlArr["Результат"] === "true"){
            return ['success' => true];
        }
        else {
            return Utils::createErrorArray($xmlArr["ОписаниеОшибки"] ?? "");
        }
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return array
     */
    public function prepareStatusResultData(SimpleXMLElement $xml): array
    {
        $xmlArr = $this->xmlToArray($xml);
        if ((int)$xmlArr["Результат"] > 0)
        {
            return [
                'success'   => true,
                'statusId'  => $xmlArr['Результат'],
                'status'    => $xmlArr['ОписаниеРезультата']
            ];
        }
        else {
            return Utils::createErrorArray($xmlArr["Результат"] ." - ". $xmlArr["ОписаниеОшибки"]);
        }
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