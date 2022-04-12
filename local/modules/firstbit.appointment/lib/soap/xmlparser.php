<?php
namespace FirstBit\Appointment\Soap;

use Exception;
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
            $clinics = [];
            if (is_array($xmlArr['Клиника'])){
                foreach ($xmlArr['Клиника'] as $item) {
                    $clinic = [];
                    $clinic['uid'] = $item['УИД'];
                    $clinic['name'] = $item['Наименование'];
                    $clinics[] = $clinic;
                }
            }

            return $clinics;
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

                    $employee['name']        = $item['Имя'];
                    $employee['surname']     = $item['Фамилия'];
                    $employee['middleName']  = $item['Отчество'];
                    $employee['clinicUid']   = $clinicUid;
                    $employee['photo']       = $item['Фото'];
                    $employee['description'] = $item['КраткоеОписание'];
                    $employee['specialty']   = $item['Специализация'];
                    $employee['services']    = [];

                    if (is_array($item['ОсновныеУслуги']['ОсновнаяУслуга']))
                    {
                        foreach ($item['ОсновныеУслуги']['ОсновнаяУслуга'] as $service)
                        {
                            $employee['services'][$service['UID']] = [
                                'title'            => 'deprecated field',
                                'personalDuration' => strtotime($service['Продолжительность'])-strtotime('0001-01-01T00:00:00')
                            ];
                        }
                    }

                    $employees[$item['UID'][0]] = $employee;
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
                    $product = [];
                    $product['name'] = $item['Наименование'];
                    $product['typeOfItem'] = $item['Вид'];
                    $product['artNumber'] = $item['Артикул'];
                    $product['price'] = $item['Цена'];
                    $product['duration'] = Utils::formatDurationToSeconds($item['Продолжительность']);
                    $nomenclature[$item['UID'][0]] = $product;
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
     * @param SimpleXMLElement $xml
     * @return array
     */
    public static function xmlToArray(SimpleXMLElement $xml): array
    {
        return json_decode(json_encode($xml), true);
    }
}