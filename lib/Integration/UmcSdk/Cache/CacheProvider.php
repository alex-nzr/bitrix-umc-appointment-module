<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 22.08.2025
 * ==================================================
*/
namespace ANZ\Appointment\Integration\UmcSdk\Cache;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Integration\UmcSdk\Exception\UmcIntegrationCacheException;
use Bitrix\Main\Context;
use Bitrix\Main\Data\Cache;
use Exception;
use Throwable;

class CacheProvider
{
    private int $ttl;
    private int $scheduleTtl = 15;
    private Cache $cacheInstance;
    private string $cachePath;
    private CacheKey $cacheKeyManager;
    private string $siteId;

    /**
     * @throws \ANZ\Appointment\Core\Exception\ConfigurationException
     */
    public function __construct()
    {
        $this->siteId = (string)Context::getCurrent()->getSite();
        if (strlen($this->siteId) === 0 && defined('SITE_ID') && strlen(SITE_ID) > 0)
        {
            $this->siteId = SITE_ID;
        }
        $this->ttl = Configuration::getInstance()->getCacheTtl();
        $this->cacheInstance = Cache::createInstance();
        $this->cachePath = Configuration::getModuleId();
        $this->cacheKeyManager = new CacheKey($this->siteId, $this->cachePath);
    }

    /**
     * @throws UmcIntegrationCacheException
     */
    public function get(string $cacheKey, int $ttl): ?array
    {
        try
        {
            if ($this->cacheInstance->initCache($ttl, $cacheKey, $this->cachePath))
            {
                return $this->cacheInstance->getVars();
            }
            return null;
        }
        catch (Throwable $e)
        {
            throw new UmcIntegrationCacheException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws UmcIntegrationCacheException
     */
    public function set(string $cacheKey, int $ttl, array $vars): void
    {
        try
        {
            if (!$this->cacheInstance->startDataCache($ttl, $cacheKey, $this->cachePath))
            {
                throw new UmcIntegrationCacheException('Can not start data cache');
            }
            $this->cacheInstance->endDataCache($vars);
        }
        catch (Throwable $e)
        {
            $this->cacheInstance->abortDataCache();
            throw new UmcIntegrationCacheException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws UmcIntegrationCacheException
     */
    public function getClinics(): ?array
    {
        return $this->get($this->cacheKeyManager->clinics(), $this->ttl);
    }

    /**
     * @throws UmcIntegrationCacheException
     */
    public function setClinics(array $data): void
    {
        $this->set($this->cacheKeyManager->clinics(), $this->ttl, $data);
    }

    /**
     * @throws UmcIntegrationCacheException
     */
    public function getEmployees(): ?array
    {
        return $this->get($this->cacheKeyManager->employees(), $this->ttl);
    }

    /**
     * @throws UmcIntegrationCacheException
     */
    public function setEmployees(array $data): void
    {
        $this->set($this->cacheKeyManager->employees(), $this->ttl, $data);
    }

    /**
     * @throws UmcIntegrationCacheException
     */
    public function getServices(string $clinicUid): ?array
    {
        return $this->get($this->cacheKeyManager->services($clinicUid), $this->ttl);
    }

    /**
     * @throws UmcIntegrationCacheException
     */
    public function setServices(array $data, string $clinicUid): void
    {
        $this->set($this->cacheKeyManager->services($clinicUid), $this->ttl, $data);
    }

    /**
     * @throws UmcIntegrationCacheException
     */
    public function getSchedule(string $clinicUid = '', array $employees = []): ?array
    {
        return $this->get($this->cacheKeyManager->schedule($clinicUid, $employees), $this->scheduleTtl);
    }

    /**
     * @throws UmcIntegrationCacheException
     */
    public function setSchedule(array $data, string $clinicUid = '', array $employees = []): void
    {
        $this->set($this->cacheKeyManager->schedule($clinicUid, $employees), $this->scheduleTtl, $data);
    }

    public function cleanByKey(string $key): void
    {
        $this->cacheInstance->clean($key, $this->cachePath);
    }

    public function cleanAll(): void
    {
        $this->cacheInstance->cleanDir($this->cachePath);
    }
}