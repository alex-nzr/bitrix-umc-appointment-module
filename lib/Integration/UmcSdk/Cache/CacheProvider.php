<?php
/*
 * ==================================================
 * This file is part of project bitrix.firstbit.umc
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
    public function get(string $cacheKey): ?array
    {
        try
        {
            $cacheTtl = $cacheKey === 'schedule' ? $this->scheduleTtl : $this->ttl;
            if ($this->cacheInstance->initCache($cacheTtl, $cacheKey, $this->cachePath))
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
    public function set(string $cacheKey, array $vars): void
    {
        try
        {
            $cacheTtl = $cacheKey === 'schedule' ? $this->scheduleTtl : $this->ttl;
            if (!$this->cacheInstance->startDataCache($cacheTtl, $cacheKey, $this->cachePath))
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
        return $this->get($this->cacheKeyManager->clinics());
    }

    /**
     * @throws \Exception
     */
    public function setClinics(array $data): void
    {
        $this->set($this->cacheKeyManager->clinics(), $data);
    }

    /**
     * @throws \Exception
     */
    public function getEmployees(): ?array
    {
        return $this->get($this->cacheKeyManager->employees());
    }

    /**
     * @throws \Exception
     */
    public function setEmployees(array $data): void
    {
        $this->set($this->cacheKeyManager->employees(), $data);
    }

    /**
     * @throws \Exception
     */
    public function getServices(string $clinicUid): ?array
    {
        return $this->get($this->cacheKeyManager->services($clinicUid));
    }

    /**
     * @throws \Exception
     */
    public function setServices(array $data, string $clinicUid): void
    {
        $this->set($this->cacheKeyManager->services($clinicUid), $data);
    }

    /**
     * @throws \Exception
     */
    public function getSchedule(string $clinicUid = '', array $employees = []): ?array
    {
        return $this->get($this->cacheKeyManager->schedule($clinicUid, $employees));
    }

    /**
     * @throws \Exception
     */
    public function setSchedule(array $data, string $clinicUid = '', array $employees = []): void
    {
        $this->set($this->cacheKeyManager->schedule($clinicUid, $employees), $data);
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