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

use Bitrix\Main\Engine\Action;
use Bitrix\Main\Engine\ActionFilter\Authentication;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Result;
use ANZ\Appointment\Service\Container;
use ANZ\Appointment\Service\OneC\Reader;
use ANZ\Appointment\Service\Operation\Appointment;

/**
 * Class OneCController
 * @package ANZ\Appointment\Controller
 */
class OneCController extends Controller
{
    private Reader $reader;

    /**
     * OneCController constructor.
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function __construct()
    {
        parent::__construct();

        $container = Container::getInstance();
        $this->reader = $container->getReaderService();
    }

    /**
     * @return \Bitrix\Main\Result
     */
    public function getClinicsAction(): Result
    {
        return $this->reader->getClinicsList();
    }

    /**
     * @return \Bitrix\Main\Result
     */
    public function getEmployeesAction(): Result
    {
        return $this->reader->getEmployeesList();
    }

    /**
     * @param string $clinicGuid
     * @return \Bitrix\Main\Result
     */
    public function getNomenclatureAction(string $clinicGuid): Result
    {
        return $this->reader->getNomenclatureList($clinicGuid);
    }

    /**
     * @return \Bitrix\Main\Result
     */
    public function getScheduleAction(): Result
    {
        return $this->reader->getSchedule();
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
     * @return \Bitrix\Main\Engine\ActionFilter\Authentication[][][]
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