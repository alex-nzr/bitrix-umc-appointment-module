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
use ANZ\Appointment\Service\Operation\Appointment;
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

/**
 * Class OneCController
 * @package ANZ\Appointment\Controller
 */
class OneCController extends Controller
{
    private Manager $exchangeService;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->exchangeService = Container::getInstance()->getExchangeManager();
    }

    /**
     * @throws \Exception
     */
    public function getClinicsAction(): array
    {
        return $this->exchangeService->getClinics();
    }

    /**
     * @throws \Exception
     */
    public function getEmployeesAction(): array
    {
        return $this->exchangeService->getEmployees();
    }

    /**
     * @throws \Exception
     */
    public function getServicesAction(string $clinicUid): array
    {
        return $this->exchangeService->getServices($clinicUid);
    }

    /**
     * @throws \Exception
     */
    public function getScheduleAction(string $clinicUid = '', string $employeeUid = ''): array
    {
        return $this->exchangeService->getSchedule(
            Configuration::getInstance()->getExchangeSchedulePeriod(),
            $clinicUid,
            !empty($employeeUid) ? [$employeeUid] : []
        );
    }

    /**
     * @throws \Throwable
     */
    public function loadDataAction(): array
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

    /**
     * @throws \Throwable
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
            return $this->exchangeService->bookSlot($clinicUid, $employeeUid, $dateTimeBegin, $serviceDuration)->toArray();
        }
        catch (Throwable $e)
        {
            $this->addError(new Error($e->getMessage()));
            return null;
        }
    }

    public function addOrderAction(string $params): Result
    {
        $arParams = json_decode($params, true);
        return Appointment::addOrder($arParams);
    }

    /**
     * @param int $id
     * @param string $orderUid
     * @return \Bitrix\Main\Result
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function deleteOrderAction(int $id, string $orderUid): Result
    {
        return Appointment::deleteOrder($id, $orderUid);
    }

    /**
     * @param int $id
     * @param string $orderUid
     * @return \Bitrix\Main\Result
     */
    public function getOrderStatusAction(int $id, string $orderUid): Result
    {
        //todo refactor this method
        return Appointment::getOrderStatus($id, $orderUid);
    }

    /**
     * @param \Bitrix\Main\Engine\Action $action
     * @param $result
     * @return array|\Bitrix\Main\HttpResponse|mixed|void|null
     */
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

    /**
     * @return array
     */
    protected function getDefaultPreFilters(): array
    {
        return [
            new HttpMethod([HttpMethod::METHOD_POST]),
            new Csrf(),
        ];
    }

    /**
     * @return array[]
     */
    public function configureActions(): array
    {
        return [
            'deleteOrder'     => [
                FilterType::EnablePrefilter->value => [
                    new Admin
                ],
            ],
        ];
    }
}