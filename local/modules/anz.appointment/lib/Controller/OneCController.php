<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - OneCController.php
 * 10.07.2022 22:37
 * ==================================================
 */
namespace ANZ\Appointment\Controller;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Internals\Contract\Service\IExchangeService;
use ANZ\Appointment\Service\Container;
use ANZ\Appointment\Service\OneC\Exchange;
use ANZ\Appointment\Service\OneC\FtpDataReader;
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
    private IExchangeService $exchangeService;

    /**
     * OneCController constructor
     * @throws \Psr\Container\NotFoundExceptionInterface|\Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->exchangeService = Container::getInstance()->getExchangeService();
    }

    /**
     * @return \Bitrix\Main\Result
     */
    public function getClinicsAction(): Result
    {
        return $this->exchangeService->getClinicsList();
    }

    /**
     * @return \Bitrix\Main\Result
     */
    public function getEmployeesAction(): Result
    {
        return $this->exchangeService->getEmployeesList();
    }

    /**
     * @param string $clinicGuid
     * @return \Bitrix\Main\Result
     */
    public function getNomenclatureAction(string $clinicGuid): Result
    {
        return $this->exchangeService->getNomenclatureList($clinicGuid);
    }

    /**
     * @return \Bitrix\Main\Result
     */
    public function getScheduleAction(): Result
    {
        return $this->exchangeService->getSchedule();
    }

    /**
     * @param string $params
     * @return \Bitrix\Main\Result
     */
    public function addOrderAction(string $params): Result
    {
        $arParams = json_decode($params, true);
        return Appointment::addOrder($arParams);
    }

    /**
     * @param int $id
     * @param string $orderUid
     * @return \Bitrix\Main\Result
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