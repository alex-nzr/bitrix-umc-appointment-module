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
use ANZ\Appointment\Dto\AppointmentDto;
use ANZ\Appointment\Dto\AppointmentStatusDto;
use ANZ\Appointment\Dto\BookingDto;
use ANZ\Appointment\Dto\ClinicDto;
use ANZ\Appointment\Dto\EmployeeDto;
use ANZ\Appointment\Dto\ServiceDto;
use ANZ\Appointment\Dto\WaitListDto;
use ANZ\Appointment\Integration\UmcSdk\Cache\CacheProvider;
use ANZ\Appointment\Integration\UmcSdk\Contract\UmcGatewayInterface;
use ANZ\Appointment\Integration\UmcSdk\Exception\GatewayException;
use ANZ\Appointment\Integration\UmcSdk\Exception\SdkDataMapperException;
use ANZ\Appointment\Integration\UmcSdk\Exception\UmcIntegrationCacheException;
use ANZ\Appointment\Integration\UmcSdk\Gateway\Client\HttpClient;
use ANZ\Appointment\Integration\UmcSdk\Gateway\Client\SoapClient;
use ANZ\Appointment\Integration\UmcSdk\Mapper\SdkRequestFromParams;
use ANZ\Appointment\Integration\UmcSdk\Mapper\SdkResponseToDto;
use ANZ\Appointment\Integration\UmcSdk\Provider\ExchangeDataProvider;
use ANZ\Appointment\Integration\UmcSdk\Validator\RequestValidator;
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
    private const CACHE_WAIT_TIMEOUT_MS = 10000;
    private const CACHE_WAIT_INTERVAL_MS = 100;

    private array $demoData;
    private ?SdkExchangeService $sdkExchangeService = null;
    private string $lockKeyPrefix = 'anz_lock_';

    /**
     * @throws GatewayException
     */
    public function __construct(
        private readonly bool                 $demoMode,
        private readonly SdkResponseToDto     $responseMapper,
        private readonly SdkRequestFromParams $requestMapper,
        private readonly ResponseValidator    $responseValidator,
        private readonly RequestValidator    $requestValidator,
        private readonly CacheProvider        $cacheProvider,
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

            $client = $exchangeMode === ExchangeMode::SOAP
                ? SoapClient::create(
                    $login,
                    $password,
                    $publicationScheme,
                    $publicationHost,
                    $baseName,
                    $scope,
                    new ExchangeDataProvider(new XmlParser)
                )
                : HttpClient::create(
                    $login,
                    $password,
                    $publicationScheme,
                    $publicationHost,
                    $baseName,
                    $scope,
                    $token
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
                $lockKey = $this->buildLockKey(__METHOD__);
                if ($this->isLocked($lockKey))
                {
                    $data = $this->waitForCache(fn() => $this->cacheProvider->getClinics(), $lockKey) ?? [];
                }
                else
                {
                    $this->setLock($lockKey);
                    try
                    {
                        $result = $this->getSdkExchangeService()->getClinics();
                        if (!$result->isSuccess())
                        {
                            throw new GatewayException(implode(PHP_EOL, $result->getErrorMessages()));
                        }

                        $data = array_filter($result->getData(), fn($item) => $this->responseValidator->validateClinic($item));
                        $result->setData([]);
                        $this->cacheProvider->setClinics($data);
                    }
                    finally
                    {
                        $this->releaseLock($lockKey);
                    }
                }
            }
        }

        return array_map(fn(array $item) => $this->responseMapper->clinicFromArray($item), $data ?? []);
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
                $lockKey = $this->buildLockKey(__METHOD__);
                if ($this->isLocked($lockKey))
                {
                    $data = $this->waitForCache(fn() => $this->cacheProvider->getEmployees(), $lockKey) ?? [];
                }
                else
                {
                    $this->setLock($lockKey);
                    try
                    {
                        $result = $this->getSdkExchangeService()->getEmployees();
                        if (!$result->isSuccess())
                        {
                            throw new GatewayException(implode(PHP_EOL, $result->getErrorMessages()));
                        }

                        $data = array_filter($result->getData(), fn($item) => $this->responseValidator->validateEmployee($item));
                        $result->setData([]);
                        $this->cacheProvider->setEmployees($data);
                    }
                    finally
                    {
                        $this->releaseLock($lockKey);
                    }
                }
            }
        }

        return array_map(fn(array $item) => $this->responseMapper->employeeFromArray($item), $data ?? []);
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
                $lockKey = $this->buildLockKey(__METHOD__, [$clinicUid]);
                if ($this->isLocked($lockKey))
                {
                    $data = $this->waitForCache(fn() => $this->cacheProvider->getServices($clinicUid), $lockKey) ?? [];
                }
                else
                {
                    $this->setLock($lockKey);
                    try
                    {
                        $result = $this->getSdkExchangeService()->getNomenclature($clinicUid);
                        if (!$result->isSuccess())
                        {
                            throw new GatewayException(implode(PHP_EOL, $result->getErrorMessages()));
                        }

                        $data = array_filter($result->getData(), fn($item) => $this->responseValidator->validateService($item));
                        $result->setData([]);
                        $this->cacheProvider->setServices($data, $clinicUid);
                    }
                    finally
                    {
                        $this->releaseLock($lockKey);
                    }
                }
            }
        }

        return array_map(fn(array $item) => $this->responseMapper->serviceFromArray($item), $data ?? []);
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
            $data = $this->cacheProvider->getSchedule($days, $clinicUid, $employees, $startDate);
            if (is_null($data))
            {
                $lockKey = $this->buildLockKey(__METHOD__, [
                    $days,
                    $clinicUid,
                    ...$employees,
                    $startDate?->format(DATE_ATOM) ?? '',
                ]);

                if ($this->isLocked($lockKey, $this->cacheProvider->getScheduleTtl()))
                {
                    $data = $this->waitForCache(
                        fn() => $this->cacheProvider->getSchedule($days, $clinicUid, $employees, $startDate),
                        $lockKey,
                        $this->cacheProvider->getScheduleTtl()
                    ) ?? [];
                }
                else
                {
                    $this->setLock($lockKey, $this->cacheProvider->getScheduleTtl());
                    try
                    {
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
                                    if ($this->responseValidator->validateScheduleItem($scheduleData))
                                    {
                                        $data[$clinicKey][$specialtyKey][$employeeKey] = $this->responseMapper->scheduleItemFromArray(
                                            $clinicKey,
                                            $specialtyKey,
                                            $employeeKey,
                                            $scheduleData
                                        );
                                    }
                                    else
                                    {
                                        unset($data[$clinicKey][$specialtyKey][$employeeKey]);
                                    }
                                }
                            }
                        }
                        $this->cacheProvider->setSchedule($data, $days, $clinicUid, $employees, $startDate);
                    }
                    finally
                    {
                        $this->releaseLock($lockKey);
                    }
                }
            }
        }
        return $data ?? [];
    }

    /**
     * @throws GatewayException
     */
    public function getAppointmentStatus(string $appointmentUid): AppointmentStatusDto
    {
        try
        {
            if ($this->demoMode)
            {
                return new AppointmentStatusDto('demo', 'Demo mode is ON');
            }

            $sdkResult = $this->getSdkExchangeService()->getOrderStatus($appointmentUid);

            $this->responseValidator->validateAppointmentStatus($sdkResult->getData());
            return $this->responseMapper->statusFromArray($sdkResult->getData());
        }
        catch (Throwable $e)
        {
            throw new GatewayException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws GatewayException
     */
    public function sendBooking(string $clinicUid, string $employeeUid, DateTime $dateTimeBegin, int $serviceDuration): BookingDto
    {
        $uid = null;
        try
        {
            $dto = new BookingDto(
                '',
                $clinicUid,
                $employeeUid,
                $dateTimeBegin->format(Configuration::DATE_FORMAT_FOR_OPTIONS),
                $serviceDuration
            );

            if ($this->demoMode)
            {
                sleep(3);
                $dto->uid = uniqid('demo_mode_uid_');
                return $dto;
            }

            $result = $this->getSdkExchangeService()->sendReserve(
                $this->requestMapper->bookingItemFromParams($clinicUid, $employeeUid, $dateTimeBegin, $serviceDuration)
            );
            if (!$result->isSuccess())
            {
                throw new GatewayException(implode(PHP_EOL, $result->getErrorMessages()));
            }

            $dto->uid = (string)$result->getData()['uid'];
            $uid = $dto->uid;
            $this->responseValidator->validateBookingItem($dto);
            return $dto;
        }
        catch (Throwable $e)
        {
            if ($uid !== null)
            {
                try {
                    $this->deleteAppointment($uid);
                }
                catch (Throwable){}
            }
            throw new GatewayException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws \ANZ\Appointment\Integration\UmcSdk\Exception\GatewayException
     */
    public function sendAppointment(array $data): AppointmentDto
    {
        try
        {
            $dto = $this->requestMapper->appointmentDtoFromArray($data);
            $this->requestValidator->validateAppointment($dto);
            if ($this->demoMode)
            {
                sleep(3);
                return $dto;
            }

            $result = $this->getSdkExchangeService()->sendOrder(
                $this->requestMapper->appointmentItemFromDto($dto)
            );
            if (!$result->isSuccess())
            {
                try
                {
                    $this->deleteAppointment($dto->uid);
                }
                catch (Throwable){}

                throw new GatewayException(implode(PHP_EOL, $result->getErrorMessages()));
            }

            return $dto;
        }
        catch (Throwable $e)
        {
            if ($e instanceof GatewayException)
            {
                throw $e;
            }
            throw new GatewayException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws \ANZ\Appointment\Integration\UmcSdk\Exception\GatewayException
     */
    public function sendWaitList(array $data): WaitListDto
    {
        try
        {
            $dto = $this->requestMapper->waitListDtoFromArray($data);
            if ($this->demoMode)
            {
                sleep(3);
                return $dto;
            }

            $result = $this->getSdkExchangeService()->sendWaitList(
                $this->requestMapper->waitListItemFromDto($dto)
            );
            if (!$result->isSuccess())
            {
                throw new GatewayException(implode(PHP_EOL, $result->getErrorMessages()));
            }
            return $dto;
        }
        catch (Throwable $e)
        {
            if ($e instanceof GatewayException)
            {
                throw $e;
            }
            throw new GatewayException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws \ANZ\Appointment\Integration\UmcSdk\Exception\GatewayException
     */
    public function deleteAppointment(string $uid): bool
    {
        $res = $this->getSdkExchangeService()->deleteOrder($uid);
        if (!$res->isSuccess())
        {
            throw new GatewayException(implode(PHP_EOL, $res->getErrorMessages()));
        }
        return true;
    }

    /**
     * @throws UmcIntegrationCacheException
     */
    private function isLocked(string $key, int $lockTime = 60): bool
    {
        return is_array($this->cacheProvider->get($key, $lockTime));
    }

    /**
     * @throws UmcIntegrationCacheException
     */
    private function setLock(string $key, int $lockTime = 60): void
    {
        $this->cacheProvider->set($key, $lockTime, [true]);
    }

    private function releaseLock(string $key): void
    {
        $this->cacheProvider->cleanByKey($key);
    }

    private function buildLockKey(string $method, array $context = []): string
    {
        $normalizedContext = array_map(
            static fn($item) => is_scalar($item) || $item === null ? (string)$item : json_encode($item, JSON_UNESCAPED_UNICODE),
            $context
        );

        return $this->lockKeyPrefix . md5($method . '|' . implode('|', $normalizedContext));
    }

    /**
     * @throws \ANZ\Appointment\Integration\UmcSdk\Exception\UmcIntegrationCacheException
     */
    private function waitForCache(callable $cacheGetter, string $lockKey, int $lockTime = 60): ?array
    {
        $deadline = microtime(true) + (self::CACHE_WAIT_TIMEOUT_MS / 1000);

        do
        {
            usleep(self::CACHE_WAIT_INTERVAL_MS * 1000);
            $cached = $cacheGetter();
            if ($cached !== null)
            {
                return $cached;
            }
        }
        while ($this->isLocked($lockKey, $lockTime) && microtime(true) < $deadline);

        return $cacheGetter();
    }
}
