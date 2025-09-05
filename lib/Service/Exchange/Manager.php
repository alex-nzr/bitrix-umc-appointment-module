<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 06.09.2025
 * ==================================================
*/
namespace ANZ\Appointment\Service\Exchange;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Integration\UmcSdk\Contract\UmcGatewayInterface;
use ANZ\Appointment\Integration\UmcSdk\Exception\GatewayException;
use ANZ\Appointment\Repository\EntityRepository;
use ANZ\Appointment\Service\Container;

class Manager
{
    public function __construct(
        protected UmcGatewayInterface $gateway,
        protected EntityRepository $repository
    )
    {
    }

    /**
     * @throws \Exception
     */
    public function renewCacheData(bool $force = false): void
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

    public function checkConnection(string $mode, string $url, string $login, string $password, string $token = ''): bool
    {
        return $this->gateway->checkConnection($mode, $url, $login, $password, $token);
    }

    public function getClinics(): array
    {
        return $this->gateway->getClinics();
    }

    public function getEmployees(): array
    {
        return $this->gateway->getEmployees();
    }

    public function getServices(string $clinicUid): array
    {
        return $this->gateway->getServices($clinicUid);
    }

    public function getSchedule(int $days, string $clinicUid, array $employees): array
    {
        return $this->gateway->getSchedule($days, $clinicUid, $employees);
    }
}