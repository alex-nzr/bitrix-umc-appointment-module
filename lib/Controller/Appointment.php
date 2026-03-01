<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.07.2022
 * ==================================================
*/
namespace ANZ\Appointment\Controller;

use ANZ\Appointment\Agent\Exchange;
use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Config\Constants;
use ANZ\Appointment\Core\ActionFilter\Admin;
use ANZ\Appointment\Service\Container;
use ANZ\Appointment\Service\Exchange\Manager;
use ANZ\Appointment\UI\Adapter\ReactMUI;
use Bitrix\Main\Engine\Action;
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
            $this->addError(new Error($e->getMessage()));
            return null;
        }
    }

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
            $this->addError(new Error($e->getMessage()));
            return null;
        }
    }

    public function getServicesAction(string $clinicUid): ?array
    {
        try
        {
            return $this->isReactMuiExt
                ? ReactMUI::prepareServicesData(
                    $this->exchangeService->getServices($clinicUid), $this->exchangeService->getEmployees()
                )
                : $this->exchangeService->getServices($clinicUid);
        }
        catch (Throwable $e)
        {
            $this->addError(new Error($e->getMessage()));
            return null;
        }
    }

    public function getScheduleAction(string $clinicUid = '', string $employeeUid = ''): ?array
    {
        try
        {
            return $this->isReactMuiExt
                    ? ReactMUI::prepareScheduleData($this->exchangeService->getSchedule(
                            Configuration::getInstance()->getExchangeSchedulePeriod(),
                            $clinicUid,
                            !empty($employeeUid) ? [$employeeUid] : []
                        ))
                : $this->exchangeService->getSchedule(
                    Configuration::getInstance()->getExchangeSchedulePeriod(),
                    $clinicUid,
                    !empty($employeeUid) ? [$employeeUid] : []
                );
        }
        catch (Throwable $e)
        {
            $this->addError(new Error($e->getMessage()));
            return null;
        }
    }

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
            $this->addError(new Error($e->getMessage()));
            return null;
        }
    }

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
            $this->addError(new Error($e->getMessage()));
            return null;
        }
    }

    public function bookSlotAction(
        string $clinicUid,
        string $employeeUid,
        string $dateTimeBegin,
        int    $serviceDuration = 0,
    ): ?array
    {
        try
        {
            return $this->exchangeService->sendBooking($clinicUid, $employeeUid, $dateTimeBegin, $serviceDuration)?->toArray();
        }
        catch (Throwable $e)
        {
            $this->addError(new Error($e->getMessage()));
            return null;
        }
    }

    public function sendAppointmentAction(string $jsonData): ?array
    {
        try
        {
            $data = json_decode($jsonData, true);
            if (!is_array($data))
            {
                throw new Exception('Invalid JSON Data');
            }

            return $this->exchangeService->sendAppointment($data)->toArray();
        }
        catch (Throwable $e)
        {
            $this->addError(new Error($e->getMessage()));
            return null;
        }
    }

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
            $this->addError(new Error($e->getMessage()));
        }
        return null;
    }

    public function updateAppointmentStatusAction(int $id, string $uid): ?array
    {
        try
        {
            return $this->exchangeService->updateAppointmentStatus($id, $uid)->toArray();
        }
        catch (Throwable $e)
        {
            $this->addError(new Error($e->getMessage()));
            return null;
        }
    }

    public function sendConfirmCodeAction(string $phone = '', string $email = ''): ?Result
    {
        try
        {
            return Container::getInstance()->getConfirmationService()->sendConfirmCode($phone, $email);
        }
        catch (Throwable $e)
        {
            $this->addError(new Error($e->getMessage()));
            return null;
        }
    }

    public function verifyConfirmCodeAction(string $code): ?Result
    {
        try
        {
            return Container::getInstance()->getConfirmationService()->verifyConfirmCode($code);
        }
        catch (Throwable $e)
        {
            $this->addError(new Error($e->getMessage()));
            return null;
        }
    }

    public function sendEmailNoteAction(string $jsonData): Result
    {
        try
        {
            $data = json_decode($jsonData, true);
            if (!is_array($data))
            {
                throw new Exception('Invalid JSON Data');
            }

            return Container::getInstance()->getMailerService()->sendEmailNote($data);
        }
        catch (Exception $e)
        {
            return (new Result)->addError(new Error($e->getMessage()));
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
        return [
            'deleteAppointment' => [
                FilterType::EnablePrefilter->value => [
                    new Admin
                ],
            ],
        ];
    }
}