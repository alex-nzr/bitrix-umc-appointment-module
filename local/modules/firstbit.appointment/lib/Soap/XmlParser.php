<?php
namespace FirstBit\Appointment\Soap;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Exception;
use FirstBit\Appointment\Config\Constants;
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
            $xmlArr = $this->getEventHandlersResult(EventType::ON_BEFORE_CLINICS_PARSED, $xmlArr);
            $clinics = [];
            if (is_array($xmlArr['Клиника'])){
                foreach ($xmlArr['Клиника'] as $item) {
                    $clinic = [];
                    $clinic['uid'] = $item['УИД'];
                    $clinic['name'] = $item['Наименование'];
                    $clinics[$item['УИД']] = $clinic;
                }
            }
            return $this->getEventHandlersResult(EventType::ON_AFTER_CLINICS_PARSED, $clinics);
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
            return $employees;
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

            return $nomenclature;
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
            $schedule = [];
            if (is_array($xmlArr['ГрафикДляСайта'])){
                $schedule = Utils::prepareScheduleData($xmlArr['ГрафикДляСайта']);
            }
            return $schedule;
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

    /**
     * @param string $eventName
     * @param $params
     * @return array|null
     * @throws \Exception
     */
    protected function getEventHandlersResult(string $eventName, $params): ?array
    {
        return $this->sendEvent($eventName, $params);
    }

    /**
     * @param string $eventName
     * @param $params
     * @return array|null
     * @throws \Exception
     */
    protected function sendEvent(string $eventName, $params): ?array
    {
        $event = new Event(
            Constants::APPOINTMENT_MODULE_ID,
            $eventName,
            $params
        );
        $event->send();

        return $this->processEventResult($event);
    }

    /**
     * @throws \Exception
     */
    protected function processEventResult(Event $event): ?array
    {
        $result = $event->getParameters();
        foreach ($event->getResults() as $eventResult)
        {
            switch($eventResult->getType())
            {
                case EventResult::ERROR:
                    throw new Exception(json_encode($event->getParameters()));
                case EventResult::SUCCESS:
                    $handlerResult = $eventResult->getParameters();
                    if (is_array($handlerResult)){
                        $result = array_merge($result, $handlerResult);
                    }
                    break;
                case EventResult::UNDEFINED:
                    // handle unexpected unknown result
                    break;
            }
        }
        return $result;
    }
}