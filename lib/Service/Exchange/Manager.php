<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 06.09.2025
 * ==================================================
*/
namespace ANZ\Appointment\Service\Exchange;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Config\Constants;
use ANZ\Appointment\Core\Exception\ExchangeManagerException;
use ANZ\Appointment\Integration\UmcSdk\Contract\UmcGatewayInterface;
use ANZ\Appointment\Repository\EntityRepository;
use ANZ\Appointment\Service\Container;
use Throwable;

class Manager
{
    public function __construct(
        protected UmcGatewayInterface $gateway,
        protected EntityRepository $repository
    )
    {
    }

    /**
     * @throws ExchangeManagerException
     */
    public function renewCacheData(bool $force = false): void
    {
        try
        {
            if ($force)
        {
            Container::getInstance()->getUmcIntegrationCacheProvider()->cleanAll();
        }

        $clinics = $this->gateway->getClinics();

        if (Configuration::getInstance()->isServicesEnabled())
        {
            foreach ($clinics as $clinic)
            {
                $this->gateway->getServices($clinic->uid);
            }
        }

        $this->gateway->getEmployees();
        }
        catch (Throwable $e)
        {
            throw new ExchangeManagerException(__METHOD__, $e);
        }
    }

    /**
     * @throws ExchangeManagerException
     */
    public function checkConnection(string $mode, string $url, string $login, string $password, string $token = ''): bool
    {
        try
        {
            if ($password === Constants::PASSWORD_MASKED_VALUE)
            {
                $password = Configuration::getInstance()->getOneCPassword();
            }
            return $this->gateway->checkConnection($mode, $url, $login, $password, $token);
        }
        catch (Throwable $e)
        {
            throw new ExchangeManagerException(__METHOD__, $e);
        }
    }

    /**
     * @throws ExchangeManagerException
     */
    public function getClinics(): array
    {
        try
        {
            return $this->gateway->getClinics();
        }
        catch (Throwable $e)
        {
            throw new ExchangeManagerException(__METHOD__, $e);
        }
    }

    /**
     * @throws ExchangeManagerException
     */
    public function getEmployees(): array
    {
        try
        {
            return $this->gateway->getEmployees();
        }
        catch (Throwable $e)
        {
            throw new ExchangeManagerException(__METHOD__, $e);
        }
    }

    /**
     * @throws ExchangeManagerException
     */
    public function getServices(string $clinicUid): array
    {
        try
        {
            return $this->gateway->getServices($clinicUid);
        }
        catch (Throwable $e)
        {
            throw new ExchangeManagerException(__METHOD__, $e);
        }
    }

    /**
     * @throws ExchangeManagerException
     */
    public function getSchedule(int $days, string $clinicUid, array $employees): array
    {
        try
        {
            return $this->gateway->getSchedule($days, $clinicUid, $employees);
        }
        catch (Throwable $e)
        {
            throw new ExchangeManagerException(__METHOD__, $e);
        }
    }
}