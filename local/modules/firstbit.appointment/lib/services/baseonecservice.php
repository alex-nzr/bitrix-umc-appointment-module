<?php
namespace FirstBit\Appointment\Services;

use Bitrix\Main\Localization\Loc;
use Exception;
use FirstBit\Appointment\Config\Constants;
use FirstBit\Appointment\Soap\UmcClient;
use FirstBit\Appointment\Utils\Utils;

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
     * @return array
     */
    public function send(string $endpoint, array $params = []): array
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

            $res = $this->client->call($endpoint, $params);
            if ($res->isSuccess())
            {
                return $res->getData();
            }
            else
            {
                return Utils::createErrorArray(implode('; ', $res->getErrorMessages()));
            }
        }
        catch (Exception $e )
        {
            return Utils::createErrorArray($e->getMessage());
        }
    }
}