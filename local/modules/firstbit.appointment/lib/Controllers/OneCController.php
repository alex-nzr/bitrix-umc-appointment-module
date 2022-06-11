<?php
namespace FirstBit\Appointment\Controllers;

use Bitrix\Main\Engine\Action;
use Bitrix\Main\Engine\ActionFilter\Authentication;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Result;
use FirstBit\Appointment\Services\Container;
use FirstBit\Appointment\Services\OneC\Reader;
use FirstBit\Appointment\Services\Operation\AppointmentOperation;

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


    public function getClinicsAction(): Result
    {
        return $this->reader->getClinicsList();
    }

    public function getEmployeesAction(): Result
    {
        return $this->reader->getEmployeesList();
    }

    public function getNomenclatureAction(string $clinicGuid): Result
    {
        return $this->reader->getNomenclatureList($clinicGuid);
    }

    public function getScheduleAction(): Result
    {
        return $this->reader->getSchedule();
    }

    public function addOrderAction(string $params): Result
    {
        $arParams = json_decode($params, true);
        return AppointmentOperation::addOrder($arParams);
    }

    public function deleteOrderAction(int $id, string $orderUid): Result
    {
        return AppointmentOperation::deleteOrder($id, $orderUid);
    }

    public function getOrderStatusAction(int $id, string $orderUid): Result
    {
        return AppointmentOperation::getOrderStatus($id, $orderUid);
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
            'deleteOrder'     => [
                '+prefilters' => [
                    new Authentication()
                ],
            ],
        ];
    }
}