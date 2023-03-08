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
    /**
     * @var \ANZ\Appointment\Service\Xml\FtpDataReader|\ANZ\Appointment\Service\OneC\Reader
     */
    private $reader;

    /**
     * OneCController constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();

        $useDemoMode  = Configuration::getInstance()->isDemoModeOn();
        $container    = Container::getInstance();
        if ($useDemoMode)
        {
            $this->reader = $container->getOneCReader();
        }
        else
        {
            $useFtpMode   = Configuration::getInstance()->isFtpModeOn();
            $this->reader = $useFtpMode ? $container->getFtpDataReader() : $container->getOneCReader();
        }
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
     * @throws \Exception
     */
    public function getEmployeesAction(): Result
    {
        return $this->reader->getEmployeesList();
    }

    /**
     * @param string $clinicGuid
     * @return \Bitrix\Main\Result
     * @throws \Exception
     */
    public function getNomenclatureAction(string $clinicGuid): Result
    {
        return $this->reader->getNomenclatureList($clinicGuid);
    }

    /**
     * @return \Bitrix\Main\Result
     * @throws \Exception
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