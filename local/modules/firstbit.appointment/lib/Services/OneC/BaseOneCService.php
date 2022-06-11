<?php
namespace FirstBit\Appointment\Services\OneC;

use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Result;
use Exception;
use FirstBit\Appointment\Config\Constants;
use FirstBit\Appointment\Soap\UmcClient;

Loc::loadMessages(__FILE__);

abstract class BaseOneCService
{
    protected UmcClient $client;
    protected $demoData;

    public function __construct()
    {
        $this->client = new UmcClient();

        if (Constants::DEMO_MODE === "Y"){
            try {
                $this->demoData = json_decode(file_get_contents(Constants::PATH_TO_DEMO_DATA_FILE), true);
            }catch (Exception $e){}
        }
    }

    /** send request to 1C database
     * @param string $endpoint
     * @param array $params
     * @return \Bitrix\Main\Result
     */
    public function send(string $endpoint, array $params = []): Result
    {
        try
        {
            if (!$this->client->isCreatedSuccessfully())
            {
                throw new Exception(
                    Loc::getMessage("FIRSTBIT_APPOINTMENT_CLIENT_FAILED") .
                    implode('; ', $this->client->getResult()->getErrorMessages())
                );
            }

            return $this->client->call($endpoint, $params);
        }
        catch (Exception $e )
        {
            $result = new Result();
            $result->addError(new Error($e->getMessage()));
            return $result;
        }
    }
}