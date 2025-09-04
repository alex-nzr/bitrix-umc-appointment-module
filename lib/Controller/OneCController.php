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
use ANZ\Appointment\Integration\UmcSdk\Contract\UmcGatewayInterface;
use ANZ\Appointment\Service\Container;
use ANZ\Appointment\Service\Operation\Appointment;
use Bitrix\Main\Engine\Action;
use Bitrix\Main\Engine\ActionFilter\Authentication;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Result;

/**
 * Class OneCController
 * @package ANZ\Appointment\Controller
 */
class OneCController extends Controller
{
    private UmcGatewayInterface $exchangeService;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->exchangeService = Container::getInstance()->getSdkGateway();
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
     * @throws \Throwable
     */
    public function executeAction(): array
    {
        Exchange::loadData(true, true);
        return [
            Constants::OPTION_KEY_EXCHANGE_NEXT_EXEC_DATE => Configuration::getInstance()->getNextExchangeExecutionDate()?->format(Configuration::DATE_FORMAT_FOR_OPTIONS)
        ];
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
                '+prefilters' => [
                    new Authentication()
                ],
            ],
        ];
    }
}