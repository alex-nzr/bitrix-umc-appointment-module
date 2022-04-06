<?php

namespace FirstBit\Appointment\Soap;

use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Result;
use Exception;
use FirstBit\Appointment\Config\Constants;
use FirstBit\Appointment\Utils\Utils;
use SimpleXMLElement;
use SoapClient;
use Bitrix\Main\Config\Option;


class UmcClient
{
    private SoapClient  $soapClient;
    private Result      $result;
    private bool        $createdSuccessfully;

    public function __construct(array $options = null)
    {
        $this->result = new Result();
        
        try {
            $url        = Option::get(Constants::APPOINTMENT_MODULE_ID, "appointment_api_ws_url");
            $login      = Option::get(Constants::APPOINTMENT_MODULE_ID, "appointment_api_db_login");
            $password   = Option::get(Constants::APPOINTMENT_MODULE_ID, "appointment_api_db_password");

            if (empty($login) || empty($password)){
                throw new Exception(Loc::getMessage("FIRSTBIT_APPOINTMENT_SOAP_AUTH_ERROR"));
            }

            if (empty($url)){
                throw new Exception(Loc::getMessage("FIRSTBIT_APPOINTMENT_SOAP_URL_ERROR"));
            }

            if ($options === null)
            {
                $options = [
                    'login'          => $login,
                    'password'       => $password,
                    'stream_context' => stream_context_create(
                        [
                            'ssl' => [
                                'verify_peer'       => false,
                                'verify_peer_name'  => false,
                            ]
                        ]
                    ),
                    'soap_version' => SOAP_1_2,
                    'trace' => 1,
                    'connection_timeout' => 5000,
                    'keep_alive' => false,
                ];
            }

            if (!class_exists('\SoapClient')) {
                throw new Exception(Loc::getMessage("FIRSTBIT_APPOINTMENT_SOAP_EXT_NOT_FOUND"));
            }

            $this->soapClient = new SoapClient($url, $options);
            $this->createdSuccessfully = true;
        }
        catch (Exception $e)
        {
            $this->createdSuccessfully = false;
            $this->result->addError(new Error($e->getMessage()));
        }
    }

    /**
     * @param string $endpoint
     * @param array $params
     * @return \Bitrix\Main\Result
     */
    public function call(string $endpoint, array $params = []): Result
    {
        try {
            $soapParams = ['parameters' => $params];

            $response = $this->soapClient->__soapCall($endpoint, $soapParams);

            try {
                $xml = new SimpleXMLElement($response->return);
            }
            catch(Exception $e){
                throw new Exception($e->getMessage() . " | " . $response->return);
            }

            $data = $this->handleXML($endpoint, $xml);
            if (!empty($data['error'])){
                $this->result->addError(new Error($data['error']));
            }
            else{
                $this->result->setData($data);
            }
        }
        catch (Exception $e){
            $this->result->addError(new Error($e->getMessage()));
        }
        return $this->result;
    }

    /**
     * @param $endpoint
     * @param \SimpleXMLElement $xml
     * @return array
     */
    protected function handleXML($endpoint, SimpleXMLElement $xml): array
    {
        $data = [];
        switch ($endpoint)
        {
            case Constants::CLINIC_ACTION_1C:
                $data = $this->prepareClinicData($xml);
                break;
            case Constants::EMPLOYEES_ACTION_1C:
                $data = $this->prepareEmployeesData($xml);
                break;
            case Constants::NOMENCLATURE_ACTION_1C:
                $data = $this->prepareNomenclatureData($xml);
                break;
            case Constants::SCHEDULE_ACTION_1C:
                $data = $this->prepareScheduleData($xml);
                break;
            case Constants::CREATE_ORDER_ACTION_1C:
                $data = $this->prepareOrderResultData($xml);
                break;
            default:
                break;
        }
        return $data;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return array
     */
    protected function prepareClinicData(SimpleXMLElement $xml): array
    {
        try
        {
            $xmlArr = Utils::xmlToArray($xml);
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
    protected function prepareEmployeesData(SimpleXMLElement $xml): array
    {
        try
        {
            $xmlArr = Utils::xmlToArray($xml);
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
    protected function prepareNomenclatureData(SimpleXMLElement $xml): array
    {
        try
        {
            $xmlArr = Utils::xmlToArray($xml);
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
            $xmlArr = Utils::xmlToArray($xml);
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
        $xmlArr = Utils::xmlToArray($xml);
        if ($xmlArr["Результат"] === "true"){
            return ['success' => true];
        }
        else {
            return Utils::createErrorArray($xmlArr["ОписаниеОшибки"]);
        }
    }

    /**
     * @return \Bitrix\Main\Result
     */
    public function getResult(): Result
    {
        return $this->result;
    }

    /**
     * @return bool
     */
    public function isCreatedSuccessfully(): bool
    {
        return $this->createdSuccessfully;
    }
}