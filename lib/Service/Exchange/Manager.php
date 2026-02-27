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
use ANZ\Appointment\Dto\AppointmentDto;
use ANZ\Appointment\Dto\AppointmentStatusDto;
use ANZ\Appointment\Dto\BookingDto;
use ANZ\Appointment\Dto\WaitListDto;
use ANZ\Appointment\Event\Event;
use ANZ\Appointment\Event\EventType;
use ANZ\Appointment\Integration\UmcSdk\Contract\UmcGatewayInterface;
use ANZ\Appointment\Model\RecordTable;
use ANZ\Appointment\Repository\EntityRepository;
use ANZ\Appointment\Service\Container;
use Bitrix\Main\Context;
use Bitrix\Main\Diag\Debug;
use DateTime;
use Exception;
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
            $this->gateway->getEmployees();

            if (Configuration::getInstance()->isServicesEnabled())
            {
                $errors = [];
                foreach ($clinics as $clinic)
                {
                    try
                    {
                        $this->gateway->getServices($clinic->uid);
                    }
                    catch (Throwable $e)
                    {
                        Debug::writeToFile(
                            [
                                'MESSAGE' => $e->getMessage(),
                                'TRACE' => $e->getTrace()
                            ],
                            __METHOD__ . ' ' . date('Y-m-d H:i:s'),
                            Configuration::getInstance()->getExchangeLogFilePath()
                        );
                        $errors[] = "$clinic->uid '$clinic->name'" . ' - ' .$e->getMessage();
                        continue;
                    }
                }

                if (Context::getCurrent()->getRequest()->isAdminSection() && !empty($errors))
                {
                    throw new ExchangeManagerException(__METHOD__, new Exception(implode(PHP_EOL, $errors)));
                }
            }
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

    /**
     * @throws \ANZ\Appointment\Core\Exception\ExchangeManagerException
     */
    public function sendBooking(string $clinicUid, string $employeeUid, string $dateTimeBegin, int $serviceDuration = 0): ?BookingDto
    {
        try
        {
            if (Configuration::getInstance()->isWaitListEnabled())
            {
                return null;
            }

            if ($serviceDuration <= 0)
            {
                $serviceDuration = Configuration::getInstance()->getDefaultAppointmentDuration();
            }

            $oDateTimeBegin = new DateTime($dateTimeBegin);

            return $this->gateway->sendBooking($clinicUid, $employeeUid, $oDateTimeBegin, $serviceDuration);
        }
        catch (Throwable $e)
        {
            throw new ExchangeManagerException(__METHOD__, $e);
        }
    }

    /**
     * @throws \ANZ\Appointment\Core\Exception\ExchangeManagerException
     */
    public function sendAppointment(array $data): AppointmentDto|WaitListDto
    {
        try
        {
            $data = Event::getEventHandlersResult(EventType::ON_BEFORE_ORDER_SEND, $data);
            if (Configuration::getInstance()->isWaitListEnabled())
            {
                $dto = $this->gateway->sendWaitList($data);
            }
            else
            {
                $dto = $this->gateway->sendAppointment($data);
                $this->repository->save(RecordTable::fromArray($data));
            }
            return $dto;
        }
        catch (Throwable $e)
        {
            throw new ExchangeManagerException(__METHOD__, $e);
        }
    }

    /**
     * @throws \ANZ\Appointment\Core\Exception\ExchangeManagerException
     */
    public function deleteAppointment(int $id, string $uid): bool
    {
        try
        {
            return $this->gateway->deleteAppointment($uid) && $this->repository->delete($id);
        }
        catch (Throwable $e)
        {
            throw new ExchangeManagerException(__METHOD__, $e);
        }
    }

    /**
     * @throws \ANZ\Appointment\Core\Exception\ExchangeManagerException
     */
    public function updateAppointmentStatus(int $id, string $uid): AppointmentStatusDto
    {
        try
        {
            $dto = $this->gateway->getAppointmentStatus($uid);
            if ($entityObject = $this->repository->getByPrimary($id))
            {
                $entityObject->setStatus_1c($dto->name);
                $this->repository->save($entityObject);
            }
            return $dto;
        }
        catch (Throwable $e)
        {
            throw new ExchangeManagerException(__METHOD__, $e);
        }
    }
}