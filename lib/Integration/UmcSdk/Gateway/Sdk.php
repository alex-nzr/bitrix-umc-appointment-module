<?php

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
use ANZ\Appointment\Event\Event;
use ANZ\Appointment\Event\EventType;
use ANZ\Appointment\Integration\UmcSdk\Cache\CacheProvider;
use ANZ\Appointment\Integration\UmcSdk\Contract\UmcGatewayInterface;
use ANZ\Appointment\Integration\UmcSdk\Exception\GatewayException;
use ANZ\Appointment\Integration\UmcSdk\Exception\SdkDataMapperException;
use ANZ\Appointment\Integration\UmcSdk\Exception\UmcIntegrationCacheException;
use ANZ\Appointment\Integration\UmcSdk\Mapper\SdkRequestFromParams;
use ANZ\Appointment\Integration\UmcSdk\Mapper\SdkResponseToDto;
use ANZ\Appointment\Integration\UmcSdk\Validator\RequestValidator;
use ANZ\Appointment\Integration\UmcSdk\Validator\ResponseValidator;
use ANZ\BitUmc\SDK\BitUmcClient;
use ANZ\BitUmc\SDK\Domain\Request\ScheduleQuery;
use ANZ\BitUmc\SDK\Transport\Auth\BasicAuth;
use ANZ\BitUmc\SDK\Transport\ConnectionOptions;
use ANZ\BitUmc\SDK\Transport\Protocol as SdkProtocol;
use ANZ\BitUmc\SDK\Transport\TransportType;
use Bitrix\Main\Localization\Loc;
use DateTime;
use Throwable;

class Sdk implements UmcGatewayInterface
{
    private const CACHE_WAIT_TIMEOUT_MS = 10000;
    private const CACHE_WAIT_INTERVAL_MS = 100;

    private array $demoData = [];
    private ?BitUmcClient $client = null;
    private string $lockKeyPrefix = 'anz_lock_';

    public function __construct(
        private readonly bool                 $demoMode,
        private readonly SdkResponseToDto     $responseMapper,
        private readonly SdkRequestFromParams $requestMapper,
        private readonly ResponseValidator    $responseValidator,
        private readonly RequestValidator     $requestValidator,
        private readonly CacheProvider        $cacheProvider,
    ) {
        try
        {
            if ($this->demoMode)
            {
                $filePath = Configuration::getInstance()->getDemoDataFilePath(true);
                if (is_file($filePath))
                {
                    $this->demoData = json_decode(file_get_contents($filePath), true) ?: [];
                }
                else
                {
                    throw new GatewayException('Demo data file not found in ' . $filePath);
                }
            }
        }
        catch(Throwable $e)
        {
            throw new GatewayException($e->getMessage(), $e->getCode(), $e);
        }
    }

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

            $this->client = $this->createClient($exchangeMode, $login, $password, $url, Configuration::getInstance()->getOneCToken());
        }
        catch(Throwable $e)
        {
            throw new GatewayException($e->getMessage(), $e->getCode(), $e);
        }
    }

    protected function getSdkClient(): BitUmcClient
    {
        if (is_null($this->client))
        {
            $this->init();
        }
        return $this->client;
    }

    protected function createClient(ExchangeMode $exchangeMode, string $login, string $password, string $url, string $token = ''): BitUmcClient
    {
        try
        {
            if ($exchangeMode !== ExchangeMode::SOAP)
            {
                throw new GatewayException('HTTP exchange mode is not supported by bit-umc-sdk 2.0.1');
            }

            $endpoint = $this->parsePublicationUrl($url);

            return new BitUmcClient(
                TransportType::SOAP,
                new ConnectionOptions(
                    protocol: $endpoint['protocol'],
                    host: $endpoint['host'],
                    baseName: $endpoint['baseName'],
                    auth: new BasicAuth($login, $password, $token !== '' ? $token : null),
                    apiKey: $token !== '' ? $token : null
                )
            );
        }
        catch(Throwable $e)
        {
            if ($e instanceof GatewayException)
            {
                throw $e;
            }
            throw new GatewayException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function parsePublicationUrl(string $url): array
    {
        $arUri = parse_url($url);
        if (!is_array($arUri))
        {
            throw new GatewayException(Loc::getMessage('ANZ_APPOINTMENT_SOAP_URL_ERROR', [
                '#ERROR#' => 'Can not parse uri'
            ]));
        }

        $scheme = (string)($arUri['scheme'] ?? '');
        $protocol = match ($scheme) {
            SdkProtocol::HTTP->value => SdkProtocol::HTTP,
            SdkProtocol::HTTPS->value => SdkProtocol::HTTPS,
            default => throw new GatewayException(Loc::getMessage('ANZ_APPOINTMENT_SOAP_URL_ERROR', [
                '#ERROR#' => 'Unexpected uri scheme - ' . $scheme
            ])),
        };

        $host = (string)($arUri['host'] ?? '');
        if ($host === '')
        {
            throw new GatewayException(Loc::getMessage('ANZ_APPOINTMENT_SOAP_URL_ERROR', [
                '#ERROR#' => 'Uri host is empty'
            ]));
        }

        if (!empty($arUri['port']))
        {
            $host .= ':' . $arUri['port'];
        }

        $path = trim((string)($arUri['path'] ?? ''), '/');
        $pathParts = array_values(array_filter(explode('/', $path), 'strlen'));
        $scopeIndex = null;
        foreach ($pathParts as $index => $part)
        {
            if (in_array(strtolower($part), ['ws', 'hs'], true))
            {
                $scopeIndex = $index;
                break;
            }
        }

        $baseName = $scopeIndex === null
            ? ($pathParts[0] ?? '')
            : implode('/', array_slice($pathParts, 0, $scopeIndex));

        $baseName = trim($baseName, '/');
        if ($baseName === '')
        {
            throw new GatewayException(Loc::getMessage('ANZ_APPOINTMENT_SOAP_URL_ERROR', [
                '#ERROR#' => 'Can not determine 1C base name from uri'
            ]));
        }

        return [
            'protocol' => $protocol,
            'host' => $host,
            'baseName' => $baseName,
        ];
    }

    public function checkConnection(string $strModeVal, string $url, string $login, string $password, string $token = ''): bool
    {
        $this->createClient(
            ExchangeMode::from($strModeVal),
            $login,
            $password,
            $url,
            $token
        )->getClinics();

        return true;
    }

    public function getClinics(): array
    {
        if ($this->demoMode)
        {
            sleep(3);
            $data = $this->demoData['clinics'] ?? [];
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
                        $data = array_filter(
                            $this->applyParseEvents(
                                EventType::ON_BEFORE_CLINICS_PARSED,
                                EventType::ON_AFTER_CLINICS_PARSED,
                                $this->getSdkClient()->getClinics()
                            ),
                            fn($item) => $this->responseValidator->validateClinic($item)
                        );
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

    public function getEmployees(): array
    {
        if ($this->demoMode)
        {
            sleep(3);
            $data = $this->demoData['employees'] ?? [];
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
                        $data = array_filter(
                            $this->applyParseEvents(
                                EventType::ON_BEFORE_EMPLOYEES_PARSED,
                                EventType::ON_AFTER_EMPLOYEES_PARSED,
                                $this->getSdkClient()->getEmployees()
                            ),
                            fn($item) => $this->responseValidator->validateEmployee($item)
                        );
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

    public function getServices(string $clinicUid): array
    {
        if ($this->demoMode)
        {
            sleep(3);
            $data = $this->demoData['services'] ?? [];
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
                        $data = array_filter(
                            $this->applyParseEvents(
                                EventType::ON_BEFORE_NOMENCLATURE_PARSED,
                                EventType::ON_AFTER_NOMENCLATURE_PARSED,
                                $this->getSdkClient()->getNomenclature($clinicUid)
                            ),
                            fn($item) => $this->responseValidator->validateService($item)
                        );
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

    public function getSchedule(int $days = 14, string $clinicUid = '', array $employees = [], ?DateTime $startDate = null): array
    {
        if ($this->demoMode)
        {
            sleep(3);
            $data = $this->demoData['schedule'] ?? [];
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
                        $data = $this->applyParseEvents(
                            EventType::ON_BEFORE_SCHEDULE_PARSED,
                            EventType::ON_AFTER_SCHEDULE_PARSED,
                            $this->getSdkClient()->getSchedule(new ScheduleQuery($days, $clinicUid, $employees, $startDate))
                        );
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

    public function getAppointmentStatus(string $appointmentUid): AppointmentStatusDto
    {
        try
        {
            if ($this->demoMode)
            {
                return new AppointmentStatusDto('demo', 'Demo mode is ON');
            }

            $data = $this->getSdkClient()->getAppointmentStatus($appointmentUid);
            $this->responseValidator->validateAppointmentStatus($data);
            return $this->responseMapper->statusFromArray($data);
        }
        catch (Throwable $e)
        {
            throw new GatewayException($e->getMessage(), $e->getCode(), $e);
        }
    }

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

            $data = $this->getSdkClient()->sendReserve(
                $this->requestMapper->bookingItemFromParams($clinicUid, $employeeUid, $dateTimeBegin, $serviceDuration)
            );

            $dto->uid = (string)($data['uid'] ?? '');
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

            $result = $this->getSdkClient()->sendAppointment(
                $this->requestMapper->appointmentItemFromDto($dto)
            );
            if (($result['success'] ?? false) !== true)
            {
                try
                {
                    $this->deleteAppointment($dto->uid);
                }
                catch (Throwable){}

                throw new GatewayException('Appointment was not created');
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

            $this->getSdkClient()->sendWaitList(
                $this->requestMapper->waitListItemFromDto($dto)
            );

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

    public function deleteAppointment(string $uid): bool
    {
        try
        {
            $data = $this->getSdkClient()->deleteAppointment($uid);
            return ($data['success'] ?? false) === true;
        }
        catch (Throwable $e)
        {
            throw new GatewayException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function applyEvent(string $eventName, array $data): array
    {
        $result = Event::getEventHandlersResult($eventName, $data);
        return is_array($result) ? $result : $data;
    }

    /**
     * @throws \Exception
     */
    private function applyParseEvents(string $beforeEventName, string $afterEventName, array $data): array
    {
        Event::getEventHandlersResult($beforeEventName, $data);
        return $this->applyEvent($afterEventName, $data);
    }

    private function isLocked(string $key, int $lockTime = 60): bool
    {
        return is_array($this->cacheProvider->get($key, $lockTime));
    }

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
