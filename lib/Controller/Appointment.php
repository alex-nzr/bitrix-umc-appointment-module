<?php
namespace ANZ\Appointment\Controller;

use ANZ\Appointment\Agent\Exchange;
use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Config\Constants;
use ANZ\Appointment\Core\ActionFilter\Admin;
use ANZ\Appointment\Core\Exception\PublicRequestException;
use ANZ\Appointment\Core\Exception\RateLimitExceededException;
use ANZ\Appointment\Service\Container;
use ANZ\Appointment\Service\Exchange\Manager;
use ANZ\Appointment\UI\Adapter\ReactMUI;
use Bitrix\Main\Engine\Action;
use Bitrix\Main\Engine\ActionFilter\Authentication;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\ActionFilter\FilterType;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Result;
use Exception;
use Throwable;

class Appointment extends Controller
{
    private const PUBLIC_ERROR_LOG_TTL = 300;

    private Manager $exchangeService;
    private bool $isReactMuiExt;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->exchangeService = Container::getInstance()->getExchangeManager();
        $this->isReactMuiExt = Configuration::getInstance()->getJsExtensionName() === Constants::JS_EXTENSION_FORM_REACT;
    }

    /**
     * @throws \Exception
     */
    public function getClinicsAction(): ?array
    {
        try
        {
            return $this->isReactMuiExt
                ? ReactMUI::prepareClinicsData($this->exchangeService->getClinics())
                : $this->exchangeService->getClinics();
        }
        catch (Throwable $e)
        {
            $this->addPublicError($e);
            return null;
        }
    }

    /**
     * @throws \Exception
     */
    public function getEmployeesAction(): ?array
    {
        try
        {
            return $this->isReactMuiExt
                ? ReactMUI::prepareEmployeesData($this->exchangeService->getEmployees())
                : $this->exchangeService->getEmployees();
        }
        catch (Throwable $e)
        {
            $this->addPublicError($e);
            return null;
        }
    }

    /**
     * @throws \Exception
     */
    public function getServicesAction(string $clinicUid): ?array
    {
        try
        {
            $services = $this->exchangeService->getServices($clinicUid);
            return $this->isReactMuiExt
                ? ReactMUI::prepareServicesData($services, $this->exchangeService->getEmployees())
                : $services;
        }
        catch (Throwable $e)
        {
            $this->addPublicError($e);
            return null;
        }
    }

    /**
     * @throws \Exception
     */
    public function getScheduleAction(string $clinicUid = '', string $employeeUid = '', array $serviceUIDs = []): ?array
    {
        try
        {
            $schedulePeriod = Configuration::getInstance()->getExchangeSchedulePeriod();
            $employees = !empty($employeeUid) ? [$employeeUid] : [];
            $schedule = $this->exchangeService->getSchedule($schedulePeriod, $clinicUid, $employees);

            return $this->isReactMuiExt
                ? ReactMUI::prepareScheduleData($schedule, $serviceUIDs)
                : $schedule;
        }
        catch (Throwable $e)
        {
            $this->addPublicError($e);
            return null;
        }
    }

    /**
     * @throws \Exception
     */
    public function loadDataAction(): ?array
    {
        try
        {
            if (Configuration::getInstance()->isDemoModeOn())
            {
                throw new Exception(Loc::getMessage("ANZ_APPOINTMENT_EXCHANGE_MANUAL_ERROR_DEMO"));
            }
            Exchange::loadData(true, true);
            return [
                Constants::OPTION_KEY_EXCHANGE_NEXT_EXEC_DATE => Configuration::getInstance()->getNextExchangeExecutionDate()?->format(Configuration::DATE_FORMAT_FOR_OPTIONS)
            ];
        }
        catch (Throwable $e)
        {
            $this->addAdminError($e);
            return null;
        }
    }

    /**
     * @throws \Exception
     */
    public function checkConnectionAction(string $mode, string $url, string $login, string $password = '', string $token = ''): ?array
    {
        try
        {
            if ($this->exchangeService->checkConnection($mode, $url, $login, $password, $token))
            {
                return [];
            }
            throw new Exception('Connection failed with unknown error');
        }
        catch (Throwable $e)
        {
            $this->addAdminError($e);
            return null;
        }
    }

    /**
     * @throws \Exception
     */
    public function bookSlotAction(
        string $clinicUid,
        string $employeeUid,
        string $dateTimeBegin,
        int    $serviceDuration = 0,
        string $captchaSid = '',
        string $captchaWord = '',
    ): ?array
    {
        try
        {
            Container::getInstance()->getCaptchaService()->assertValid($captchaSid, $captchaWord);
            Container::getInstance()->getRateLimitPolicy()->assertBookSlotAllowed($clinicUid, $employeeUid);
            $booking = $this->exchangeService->sendBooking($clinicUid, $employeeUid, $dateTimeBegin, $serviceDuration);
            if ($booking !== null)
            {
                Container::getInstance()->getBookingSession()->markCaptchaVerified($booking->uid);
            }

            return $booking?->toArray();
        }
        catch (Throwable $e)
        {
            $this->addPublicError($e, Loc::getMessage('ANZ_APPOINTMENT_PUBLIC_ERROR_BOOK_SLOT'));
            return null;
        }
    }

    /**
     * @throws \Exception
     */
    public function sendAppointmentAction(string $jsonData, string $captchaSid = '', string $captchaWord = ''): ?array
    {
        try
        {
            Container::getInstance()->getRateLimitPolicy()->assertSendAppointmentAllowed();
            $data = json_decode($jsonData, true);
            if (!is_array($data))
            {
                throw new PublicRequestException('Invalid JSON Data');
            }

            if (Configuration::getInstance()->getExchangeConfirmMode() === 'none')
            {
                $bookingUid = (string)($data['bookingUid'] ?? '');
                if ($bookingUid !== '')
                {
                    Container::getInstance()->getBookingSession()->assertCanConfirm($bookingUid);
                }
                else
                {
                    Container::getInstance()->getCaptchaService()->assertValid($captchaSid, $captchaWord);
                }
            }

            return $this->exchangeService->sendAppointment($data)->toArray();
        }
        catch (Throwable $e)
        {
            $this->addPublicError($e, Loc::getMessage('ANZ_APPOINTMENT_PUBLIC_ERROR_CREATE_APPOINTMENT'));
            return null;
        }
    }

    /**
     * @throws \Exception
     */
    public function deleteAppointmentAction(int $id, string $uid): ?array
    {
        try
        {
            if ($this->exchangeService->deleteAppointment($id, $uid))
            {
                return [];
            }
        }
        catch (Throwable $e)
        {
            $this->addAdminError($e);
        }
        return null;
    }

    /**
     * @throws \Exception
     */
    public function cancelOwnAppointmentAction(string $uid): ?array
    {
        try
        {
            Container::getInstance()->getRateLimitPolicy()->assertCancelAppointmentAllowed($uid);
            if ($this->exchangeService->cancelOwnAppointment($uid))
            {
                return [];
            }
        }
        catch (Throwable $e)
        {
            $this->addPublicError($e, Loc::getMessage('ANZ_APPOINTMENT_PUBLIC_ERROR_CANCEL_APPOINTMENT'));
        }
        return null;
    }

    /**
     * @throws \Exception
     */
    public function updateAppointmentStatusAction(int $id, string $uid): ?array
    {
        try
        {
            return $this->exchangeService->updateAppointmentStatus($id, $uid)->toArray();
        }
        catch (Throwable $e)
        {
            $this->addAdminError($e);
            return null;
        }
    }

    /**
     * @throws \Exception
     */
    public function getCaptchaAction(): ?array
    {
        try
        {
            Container::getInstance()->getRateLimitPolicy()->assertCaptchaIssueAllowed();
            return Container::getInstance()->getCaptchaService()->create();
        }
        catch (Throwable $e)
        {
            $this->addPublicError($e);
            return null;
        }
    }

    /**
     * @throws \Exception
     */
    public function sendConfirmCodeAction(
        string $phone = '',
        string $email = '',
        string $bookingUid = '',
    ): ?Result
    {
        try
        {
            return Container::getInstance()->getConfirmationService()->sendConfirmCode($phone, $email, $bookingUid);
        }
        catch (Throwable $e)
        {
            $this->addPublicError($e);
            return null;
        }
    }

    /**
     * @throws \Exception
     */
    public function verifyConfirmCodeAction(string $code, string $bookingUid = ''): ?Result
    {
        try
        {
            return Container::getInstance()->getConfirmationService()->verifyConfirmCode($code, $bookingUid);
        }
        catch (Throwable $e)
        {
            $this->addPublicError($e);
            return null;
        }
    }

    /**
     * @throws \Exception
     */
    public function resetConfirmationAction(string $bookingUid = ''): ?array
    {
        try
        {
            Container::getInstance()->getConfirmationService()->resetUnverified($bookingUid);
            return [];
        }
        catch (Throwable $e)
        {
            $this->addPublicError($e);
            return null;
        }
    }

    /**
     * @throws \Exception
     */
    public function sendEmailNoteAction(string $jsonData): ?Result
    {
        try
        {
            $data = json_decode($jsonData, true);
            if (!is_array($data))
            {
                throw new PublicRequestException('Invalid JSON Data');
            }

            $email = (string)($data['email'] ?? '');
            Container::getInstance()->getBookingSession()->assertCanSendEmailNote($email);
            Container::getInstance()->getRateLimitPolicy()->assertEmailNoteAllowed($email);

            return Container::getInstance()->getMailerService()->sendEmailNote($data);
        }
        catch (Exception $e)
        {
            $this->addPublicError($e, Loc::getMessage('ANZ_APPOINTMENT_PUBLIC_ERROR_EMAIL_NOTE'));
            return null;
        }
    }

    protected function processAfterAction(Action $action, $result)
    {
        if ($result instanceof Result)
        {
            if ($result->isSuccess())
            {
                return $result->getData();
            }
            else
            {
                $errors = $result->getErrors();
                if (is_array($errors))
                {
                    foreach ($errors as $error)
                    {
                        $this->addError($error);
                    }
                }
                return null;
            }
        }
        return $result;
    }

    protected function getDefaultPreFilters(): array
    {
        return [
            new HttpMethod([HttpMethod::METHOD_POST]),
            new Csrf(),
        ];
    }

    public function configureActions(): array
    {
        $adminFilters = [
            new Authentication(),
            new Admin(),
        ];

        return [
            'loadData' => [
                FilterType::EnablePrefilter->value => $adminFilters,
            ],
            'checkConnection' => [
                FilterType::EnablePrefilter->value => $adminFilters,
            ],
            'deleteAppointment' => [
                FilterType::EnablePrefilter->value => $adminFilters,
            ],
            'updateAppointmentStatus' => [
                FilterType::EnablePrefilter->value => $adminFilters,
            ],
        ];
    }

    /**
     * @throws \Exception
     */
    private function addPublicError(Throwable $e, ?string $message = null): void
    {
        if ($this->shouldLogPublicError($e))
        {
            Container::getInstance()->getLogger()->log(
                $e,
                __METHOD__,
                Configuration::getInstance()->getControllerLogFilePath()
            );
        }
        $this->addError(new Error($message ?: Loc::getMessage('ANZ_APPOINTMENT_PUBLIC_ERROR_DEFAULT')));
    }

    private function shouldLogPublicError(Throwable $exception): bool
    {
        if ($exception instanceof RateLimitExceededException)
        {
            return $exception->isFirstExceeded();
        }

        if ($exception instanceof PublicRequestException)
        {
            return false;
        }

        try
        {
            return Container::getInstance()->getRateLimiter()->isFirstOccurrence(
                'public_error_log',
                self::PUBLIC_ERROR_LOG_TTL,
                get_class($exception) . '|' . $exception->getCode(),
            );
        }
        catch (Throwable)
        {
            return true;
        }
    }

    /**
     * @throws \Exception
     */
    private function addAdminError(Throwable $e): void
    {
        Container::getInstance()->getLogger()->log(
            $e,
            __METHOD__,
            Configuration::getInstance()->getControllerLogFilePath()
        );
        $this->addError(new Error($e->getMessage()));
    }
}
