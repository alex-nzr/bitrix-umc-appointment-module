<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 22.08.2025
 * ==================================================
*/
namespace ANZ\Appointment\Integration\UmcSdk\Cache;

use ANZ\Appointment\Config\Configuration;
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
     * @throws \Exception
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
     * @throws \Exception
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
            //todo UmcServiceException
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function set(string $cacheKey, int $ttl, array $vars): void
    {
        try
        {
            if (!$this->cacheInstance->startDataCache($ttl, $cacheKey, $this->cachePath))
            {
                throw new Exception('Can not start data cache');
            }
            $this->cacheInstance->endDataCache($vars);
        }
        catch (Throwable $e)
        {
            $this->cacheInstance->abortDataCache();

            //todo UmcServiceException
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function getClinics(): ?array
    {
        return $this->get($this->cacheKeyManager->clinics(), $this->ttl);
    }

    /**
     * @throws \Exception
     */
    public function setClinics(array $data): void
    {
        $this->set($this->cacheKeyManager->clinics(), $this->ttl, $data);
    }

    /**
     * @throws \Exception
     */
    public function getEmployees(): ?array
    {
        return $this->get($this->cacheKeyManager->employees(), $this->ttl);
    }

    /**
     * @throws \Exception
     */
    public function setEmployees(array $data): void
    {
        $this->set($this->cacheKeyManager->employees(), $this->ttl, $data);
    }

    /**
     * @throws \Exception
     */
    public function getServices(string $clinicUid): ?array
    {
        return $this->get($this->cacheKeyManager->services($clinicUid), $this->ttl);
    }

    /**
     * @throws \Exception
     */
    public function setServices(array $data, string $clinicUid): void
    {
        $this->set($this->cacheKeyManager->services($clinicUid), $this->ttl, $data);
    }

    /**
     * @throws \Exception
     */
    public function getSchedule(string $clinicUid = '', array $employees = []): ?array
    {
        return $this->get($this->cacheKeyManager->schedule($clinicUid, $employees), $this->scheduleTtl);
    }

    /**
     * @throws \Exception
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