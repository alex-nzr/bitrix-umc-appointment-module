<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 22.08.2025
 * ==================================================
*/
namespace ANZ\Appointment\Integration\UmcSdk\Gateway;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Config\ExchangeMode;
use ANZ\Appointment\Dto\AppointmentStatusDto;
use ANZ\Appointment\Dto\ClinicDto;
use ANZ\Appointment\Dto\EmployeeDto;
use ANZ\Appointment\Dto\ServiceDto;
use ANZ\Appointment\Integration\UmcSdk\Cache\CacheProvider;
use ANZ\Appointment\Integration\UmcSdk\Contract\UmcGatewayInterface;
use ANZ\Appointment\Integration\UmcSdk\Exception\GatewayException;
use ANZ\Appointment\Integration\UmcSdk\Exception\SdkDataMapperException;
use ANZ\Appointment\Integration\UmcSdk\Exception\UmcIntegrationCacheException;
use ANZ\Appointment\Integration\UmcSdk\Gateway\Client\SoapClient;
use ANZ\Appointment\Integration\UmcSdk\Mapper\SdkResponseToDto;
use ANZ\Appointment\Integration\UmcSdk\Provider\ExchangeDataProvider;
use ANZ\Appointment\Integration\UmcSdk\Validator\ResponseValidator;
use ANZ\BitUmc\SDK\Core\Dictionary\ClientScope;
use ANZ\BitUmc\SDK\Core\Dictionary\Protocol;
use ANZ\BitUmc\SDK\Factory\Exchange as ExchangeFactory;
use ANZ\BitUmc\SDK\Service\Exchange\Base as SdkExchangeService;
use ANZ\BitUmc\SDK\Service\Exchange\Http;
use ANZ\BitUmc\SDK\Service\Exchange\Soap;
use ANZ\BitUmc\SDK\Service\XmlParser;
use Bitrix\Main\Localization\Loc;
use DateTime;
use Throwable;

class Sdk implements UmcGatewayInterface
{
    private array $demoData;
    private ?SdkExchangeService $sdkExchangeService = null;
    private string $lockKeyPrefix = 'anz_lock_';

    /**
     * @throws GatewayException
     */
    public function __construct(
        private readonly bool                $demoMode,
        private readonly SdkResponseToDto    $mapper,
        private readonly ResponseValidator   $validator,
        private readonly CacheProvider       $cacheProvider,
    ) {
        try
        {
            if ($this->demoMode)
            {
                $filePath = Configuration::getInstance()->getDemoDataFilePath(true);
                if (is_file($filePath))
                {
                    $this->demoData = json_decode(file_get_contents($filePath), true);
                }
                else
                {
                    $this->demoData = [];
                    throw new GatewayException('Demo data file not found in ' . $filePath);
                }
            }
        }
        catch(Throwable $e)
        {
            throw new GatewayException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws \ANZ\Appointment\Integration\UmcSdk\Exception\GatewayException
     */
    protected function init(): void
    {
        try
        {
            $exchangeMode = Configuration::getInstance()->getExchangeMode();
            if (is_null($exchangeMode))
            {
                throw new GatewayException(Loc::getMessage('ANZ_APPOINTMENT_EXCHANGE_MODE_ERROR'));
            }

            $login = Configuration::getInstance()->getOneCLogin();
            $password = Configuration::getInstance()->getOneCPassword();
            if (empty($login) || empty($password))
            {
                throw new GatewayException(Loc::getMessage('ANZ_APPOINTMENT_SOAP_AUTH_ERROR'));
            }

            $url = Configuration::getInstance()->getOneCApiUrl();
            if (strlen($url) === 0)
            {
                throw new GatewayException(Loc::getMessage('ANZ_APPOINTMENT_SOAP_URL_ERROR', [
                    '#ERROR#' => 'Url is empty'
                ]));
            }

            $this->sdkExchangeService = $this->createExchangeService($exchangeMode, $login, $password, $url);
        }
        catch(Throwable $e)
        {
            throw new GatewayException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws GatewayException
     */
    protected function getSdkExchangeService(): ?SdkExchangeService
    {
        if (is_null($this->sdkExchangeService))
        {
            $this->init();
        }
        return $this->sdkExchangeService;
    }

    /**
     * @throws GatewayException
     */
    protected function createExchangeService(ExchangeMode $exchangeMode, $login, $password, $url, $token = ''): Http|Soap
    {
        try
        {
            $scope = $exchangeMode === ExchangeMode::SOAP ? ClientScope::WEB_SERVICE : ClientScope::HTTP_SERVICE;

            $arUri = parse_url($url);
            if (!in_array($arUri['scheme'], [Protocol::HTTP->value, Protocol::HTTPS->value]))
            {
                throw new GatewayException(Loc::getMessage('ANZ_APPOINTMENT_SOAP_URL_ERROR', [
                    '#ERROR#' => 'Unexpected uri scheme - ' . $arUri['scheme']
                ]));
            }
            $publicationScheme = $arUri['scheme'] === Protocol::HTTPS->value ? Protocol::HTTPS : Protocol::HTTP;

            if (strlen($arUri['host']) === 0)
            {
                throw new GatewayException(Loc::getMessage('ANZ_APPOINTMENT_SOAP_URL_ERROR', [
                    '#ERROR#' => 'Uri host is empty'
                ]));
            }
            $port = key_exists('port', $arUri) && !empty($arUri['port']) ? $arUri['port'] : null;
            $publicationHost = $arUri['host'] . (is_null($port) ? '' : ":$port");

            $path = $arUri['path'];
            if (!str_contains($path, '/'.$scope->value.'/'))
            {
                throw new GatewayException(Loc::getMessage('ANZ_APPOINTMENT_SOAP_URL_ERROR', [
                    '#ERROR#' => 'Uri path not contains exchange scope - ' . '/'.$scope->value.'/'
                ]));
            }
            $arPath = explode('/'.$scope->value.'/', $path);
            $baseName = trim(current($arPath), '/');
            if (strlen($baseName) === 0)
            {
                throw new GatewayException(Loc::getMessage('ANZ_APPOINTMENT_SOAP_URL_ERROR', [
                    '#ERROR#' => 'Can not determine 1C base name from uri'
                ]));
            }

            $client = SoapClient::create(
                $login,
                $password,
                $publicationScheme,
                $publicationHost,
                $baseName,
                $scope,
                new ExchangeDataProvider(new XmlParser)
            );

            return (new ExchangeFactory($client))->create();
        }
        catch(Throwable $e)
        {
            throw new GatewayException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws GatewayException
     */
    public function checkConnection(string $strModeVal, string $url, string $login, string $password, string $token = ''): bool
    {
        $result = $this->createExchangeService(
            ExchangeMode::from($strModeVal),
            $login,
            $password,
            $url,
            $token
        )->getClinics();

        if (!$result->isSuccess())
        {
            throw new GatewayException(implode(PHP_EOL, $result->getErrorMessages()));
        }
        return true;
    }

    /**
     * @return ClinicDto[]
     * @throws GatewayException | UmcIntegrationCacheException
     */
    public function getClinics(): array
    {
        if ($this->demoMode)
        {
            sleep(3);
            try
            {
                $data = $this->demoData['clinics'];
            }
            catch (Throwable)
            {
                $data = [];
            }
        }
        else
        {
            $data = $this->cacheProvider->getClinics();
            if (is_null($data))
            {
                if (!$this->isLocked(__METHOD__))
                {
                    $this->setLock(__METHOD__);
                    $result = $this->getSdkExchangeService()->getClinics();
                    if (!$result->isSuccess())
                    {
                        throw new GatewayException(implode(PHP_EOL, $result->getErrorMessages()));
                    }

                    $data = array_filter($result->getData(), fn($item) => $this->validator->validateClinic($item));
                    $result->setData([]);
                    $this->cacheProvider->setClinics($data);
                    $this->releaseLock(__METHOD__);
                }
            }
        }

        return array_map(fn(array $item) => $this->mapper->clinicFromArray($item), $data);
    }

    /**
     * @return EmployeeDto[]
     * @throws GatewayException | UmcIntegrationCacheException
     */
    public function getEmployees(): array
    {
        if ($this->demoMode)
        {
            sleep(3);
            try
            {
                $data = $this->demoData['employees'];
            }
            catch (Throwable)
            {
                $data = [];
            }
        }
        else
        {
            $data = $this->cacheProvider->getEmployees();
            if (is_null($data))
            {
                if (!$this->isLocked(__METHOD__))
                {
                    $this->setLock(__METHOD__);
                    $result = $this->getSdkExchangeService()->getEmployees();
                    if (!$result->isSuccess())
                    {
                        throw new GatewayException(implode(PHP_EOL, $result->getErrorMessages()));
                    }

                    $data = array_filter($result->getData(), fn($item) => $this->validator->validateEmployee($item));
                    $result->setData([]);
                    $this->cacheProvider->setEmployees($data);
                    $this->releaseLock(__METHOD__);
                }
            }
        }

        return array_map(fn(array $item) => $this->mapper->employeeFromArray($item), $data);
    }

    /**
     * @return ServiceDto[]
     * @throws GatewayException | UmcIntegrationCacheException
     */
    public function getServices(string $clinicUid): array
    {
        if ($this->demoMode)
        {
            sleep(3);
            try
            {
                $data = $this->demoData['services'];
            }
            catch (Throwable)
            {
                $data = [];
            }
        }
        else
        {
            $data = $this->cacheProvider->getServices($clinicUid);
            if (is_null($data))
            {
                if (!$this->isLocked(__METHOD__))
                {
                    $this->setLock(__METHOD__);
                    $result = $this->getSdkExchangeService()->getNomenclature($clinicUid);
                    if (!$result->isSuccess())
                    {
                        throw new GatewayException(implode(PHP_EOL, $result->getErrorMessages()));
                    }

                    $data = array_filter($result->getData(), fn($item) => $this->validator->validateService($item));
                    $result->setData([]);
                    $this->cacheProvider->setServices($data, $clinicUid);
                    $this->releaseLock(__METHOD__);
                }
            }
        }

        return array_map(fn(array $item) => $this->mapper->serviceFromArray($item), $data);
    }

    /**
     * @throws GatewayException | SdkDataMapperException | UmcIntegrationCacheException
     */
    public function getSchedule(int $days = 14, string $clinicUid = '', array $employees = [], ?DateTime $startDate = null): array
    {
        if ($this->demoMode)
        {
            sleep(3);
            try
            {
                $data = $this->demoData['schedule'];
            }
            catch (Throwable)
            {
                $data = [];
            }
        }
        else
        {
            $data = $this->cacheProvider->getSchedule($clinicUid, $employees);
            if (is_null($data))
            {
                if (!$this->isLocked(__METHOD__))
                {
                    $this->setLock(__METHOD__);
                    $result = $this->getSdkExchangeService()->getSchedule($days, $clinicUid, $employees, $startDate);
                    if (!$result->isSuccess())
                    {
                        throw new GatewayException(implode(PHP_EOL, $result->getErrorMessages()));
                    }

                    $data = $result->getData();
                    $result->setData([]);
                    foreach ($data as $clinicKey => $clinicData)
                    {
                        foreach ($clinicData as $specialtyKey => $specialtyData)
                        {
                            foreach ($specialtyData as $employeeKey => $scheduleData)
                            {
                                if ($this->validator->validateScheduleItem($scheduleData))
                                {
                                    $data[$clinicKey][$specialtyKey][$employeeKey] = $this->mapper->scheduleItemFromArray(
                                        $clinicKey, $specialtyKey, $employeeKey, $scheduleData
                                    );
                                }
                                else
                                {
                                    unset($data[$clinicKey][$specialtyKey][$employeeKey]);
                                }
                            }
                        }
                    }
                    $this->cacheProvider->setSchedule($data, $clinicUid, $employees);
                    $this->releaseLock(__METHOD__);
                }
            }
        }
        return $data;
    }

    public function getAppointmentStatus(string $orderUid): AppointmentStatusDto
    {
        // TODO: Implement getOrderStatus() method.
    }

    public function bookSlot($reserve): array
    {
        // TODO: Implement sendReserve() method.
    }

    public function addWaitList($waitList): array
    {
        // TODO: Implement sendWaitList() method.
    }

    public function sendAppointment($order): array
    {
        // TODO: Implement sendOrder() method.
    }

    public function deleteAppointment(string $orderUid): array
    {
        // TODO: Implement deleteOrder() method.
    }

    /**
     * @throws UmcIntegrationCacheException
     */
    private function isLocked(string $key): bool
    {
        return is_array($this->cacheProvider->get($key, 60));
    }

    /**
     * @throws UmcIntegrationCacheException
     */
    private function setLock(string $key): void
    {
        $this->cacheProvider->set($this->lockKeyPrefix . $key, 60, [true]);
    }

    private function releaseLock(string $key): void
    {
        $this->cacheProvider->cleanByKey($this->lockKeyPrefix . $key);
    }
}