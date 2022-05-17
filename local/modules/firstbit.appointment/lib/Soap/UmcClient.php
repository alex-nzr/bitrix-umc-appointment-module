<?php

namespace FirstBit\Appointment\Soap;

use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Result;
use Exception;
use FirstBit\Appointment\Config\Constants;
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
        
        try
        {
            if ($options === null)
            {
                $options = $this->getDefaultOptions();
            }
            else
            {
                $options = array_merge($this->getDefaultOptions(), $options);
            }

            if (!class_exists('\SoapClient')) {
                throw new Exception(Loc::getMessage("FIRSTBIT_APPOINTMENT_SOAP_EXT_NOT_FOUND"));
            }

            $url = Option::get(Constants::APPOINTMENT_MODULE_ID, "appointment_api_ws_url");
            if (empty($url)){
                throw new Exception(Loc::getMessage("FIRSTBIT_APPOINTMENT_SOAP_URL_ERROR"));
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
        $parser = new XmlParser();
        $data = [];
        switch ($endpoint)
        {
            case Constants::CLINIC_ACTION_1C:
                $data = $parser->prepareClinicData($xml);
                break;
            case Constants::EMPLOYEES_ACTION_1C:
                $data = $parser->prepareEmployeesData($xml);
                break;
            case Constants::NOMENCLATURE_ACTION_1C:
                $data = $parser->prepareNomenclatureData($xml);
                break;
            case Constants::SCHEDULE_ACTION_1C:
                $data = $parser->prepareScheduleData($xml);
                break;
            case Constants::CREATE_RESERVE_ACTION_1C:
                $data = $parser->prepareReserveResultData($xml);
                break;
            case Constants::CREATE_ORDER_ACTION_1C:
                $data = $parser->prepareOrderResultData($xml);
                break;
            case Constants::CREATE_WAIT_LIST_ACTION_1C:
                $data = $parser->prepareWaitListResultData($xml);
                break;
            case Constants::DELETE_ORDER_ACTION_1C:
                $data = $parser->prepareDeleteResultData($xml);
                break;
            case Constants::GET_ORDER_STATUS_ACTION_1C:
                $data = $parser->prepareStatusResultData($xml);
                break;
            default:
                break;
        }
        return $data;
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

    /**
     * @return array
     * @throws \Exception
     */
    protected function getDefaultOptions(): array
    {
        $login      = Option::get(Constants::APPOINTMENT_MODULE_ID, "appointment_api_db_login");
        $password   = Option::get(Constants::APPOINTMENT_MODULE_ID, "appointment_api_db_password");

        if (empty($login) || empty($password)){
            throw new Exception(Loc::getMessage("FIRSTBIT_APPOINTMENT_SOAP_AUTH_ERROR"));
        }

        return [
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
}