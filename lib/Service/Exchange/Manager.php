<?php
namespace ANZ\Appointment\Service\Exchange;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Config\Constants;
use ANZ\Appointment\Core\Exception\ExchangeManagerException;
use ANZ\Appointment\Dto\AppointmentDto;
use ANZ\Appointment\Dto\AppointmentStatusDto;
use ANZ\Appointment\Dto\BookingDto;
use ANZ\Appointment\Dto\EmployeeDto;
use ANZ\Appointment\Dto\ServiceDto;
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
            $selectedClinics = Configuration::getInstance()->getSelectedClinics();
            if (!empty($selectedClinics))
            {
                $clinics = array_values(array_filter(
                    $clinics,
                    static fn($clinic) => in_array($clinic->uid, $selectedClinics, true)
                ));
            }
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
                                'ERROR_CODE' => $e->getCode(),
                                'CLINIC' => "$clinic->uid '$clinic->name'",
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
            Container::getInstance()->getOneCUrlGuard()->assertAllowed($url);
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
     * @return EmployeeDto[]
     * @throws \ANZ\Appointment\Core\Exception\ExchangeManagerException
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
     * @param string $clinicUid
     * @return ServiceDto[]
     * @throws \ANZ\Appointment\Core\Exception\ExchangeManagerException
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

            $serviceDuration = Container::getInstance()->getAppointmentPayloadGuard()->assertBookingCanBeCreated(
                $this,
                $clinicUid,
                $employeeUid,
                $dateTimeBegin,
                $serviceDuration
            );

            $oDateTimeBegin = new DateTime($dateTimeBegin);

            $dto = $this->gateway->sendBooking($clinicUid, $employeeUid, $oDateTimeBegin, $serviceDuration);
            Container::getInstance()->getConfirmationService()->clear();
            Container::getInstance()->getBookingSession()->rememberBooking([
                'uid' => $dto->uid,
                'clinicUid' => $clinicUid,
                'employeeUid' => $employeeUid,
                'timeBegin' => $dto->dateTimeBegin,
                'serviceDuration' => $serviceDuration,
                'appointmentCreated' => false,
            ]);

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
    public function sendAppointment(array $data): AppointmentDto|WaitListDto
    {
        try
        {
            $data = Event::getEventHandlersResult(EventType::ON_BEFORE_ORDER_SEND, $data);
            $bookingUid = (string)($data['bookingUid'] ?? '');
            $booking = $bookingUid !== '' ? Container::getInstance()->getBookingSession()->get($bookingUid) : null;
            Container::getInstance()->getAppointmentPayloadGuard()->assertAppointmentPayload(
                $this,
                $data,
                $booking,
                !Configuration::getInstance()->isWaitListEnabled()
            );
            Container::getInstance()->getConfirmationService()->assertVerified(
                (string)($data['phone'] ?? ''),
                (string)($data['email'] ?? '')
            );

            if (Configuration::getInstance()->isWaitListEnabled())
            {
                $dto = $this->gateway->sendWaitList($data);
            }
            else
            {
                $dto = $this->gateway->sendAppointment($data);
                $this->repository->save(RecordTable::fromAppointmentPayload($data, $dto));
                Container::getInstance()->getBookingSession()->markAppointmentCreated($dto->uid, $data);
            }
            Container::getInstance()->getConfirmationService()->clear();
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
            if ($id > 0)
            {
                $record = $this->repository->getByPrimary($id);
                if (is_null($record) || $record->getXmlId() !== $uid
                )
                {
                    return false;
                }
            }

            if (!$this->gateway->deleteAppointment($uid))
            {
                return false;
            }

            return $id > 0 ? $this->repository->delete($id) : true;
        }
        catch (Throwable $e)
        {
            throw new ExchangeManagerException(__METHOD__, $e);
        }
    }

    /**
     * @throws \ANZ\Appointment\Core\Exception\ExchangeManagerException
     */
    public function cancelOwnAppointment(string $uid): bool
    {
        try
        {
            $accessData = Container::getInstance()->getAppointmentAccess()->assertCanCancelPublic($uid);
            $isPendingSessionBooking = is_array($accessData) && empty($accessData['appointmentCreated']) && !isset($accessData[RecordTable::FIELD_NAME_PATIENT_PHONE]);
            if (!$isPendingSessionBooking)
            {
                Container::getInstance()->getConfirmationService()->assertVerified(
                    (string)($accessData[RecordTable::FIELD_NAME_PATIENT_PHONE] ?? $accessData['phone'] ?? ''),
                    (string)($accessData[RecordTable::FIELD_NAME_PATIENT_EMAIL] ?? $accessData['email'] ?? ''),
                    'cancel'
                );
            }

            if (!$this->gateway->deleteAppointment($uid))
            {
                return false;
            }

            if (!empty($accessData[RecordTable::FIELD_NAME_ID]))
            {
                $this->repository->delete((int)$accessData[RecordTable::FIELD_NAME_ID]);
            }

            Container::getInstance()->getBookingSession()->forget($uid);
            Container::getInstance()->getConfirmationService()->clear('cancel');
            return true;
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
