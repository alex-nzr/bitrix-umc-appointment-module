<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - BaseService.php
 * 10.07.2022 22:37
 * ==================================================
 */
namespace ANZ\Appointment\Service\OneC;

use ANZ\Appointment\Internals\Control\ServiceManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Result;
use Bitrix\Main\Web\Json;
use Exception;
use ANZ\Appointment\Config\Constants;
use ANZ\Appointment\Soap\UmcClient;
Loc::loadMessages(__FILE__);

/**
 * Class BaseService
 * @package ANZ\Appointment\Service\OneC
 */
abstract class BaseService
{
    protected UmcClient $client;
    protected array $demoData;
    protected bool $demoMode;

    public function __construct()
    {
        $this->demoMode = Option::get(ServiceManager::getModuleId(), Constants::OPTION_KEY_DEMO_MODE) === 'Y';
        if ($this->demoMode)
        {
            try
            {
                if (is_file(Constants::PATH_TO_DEMO_DATA_FILE))
                {
                    $this->demoData = Json::decode(file_get_contents(Constants::PATH_TO_DEMO_DATA_FILE));
                }
                else
                {
                    throw new Exception('Demo data file not found');
                }
            }
            catch (Exception $e)
            {
                //log error
                $this->demoData = [];
            }
        }

        $this->client = new UmcClient();
    }

    /**
     * send request to 1C database
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
                    Loc::getMessage("ANZ_APPOINTMENT_CLIENT_FAILED") .
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