<?php
namespace FirstBit\Appointment\Controllers;

use Bitrix\Main\Config\Option;
use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Engine\Action;
use Bitrix\Main\Engine\ActionFilter\Authentication;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Result;
use FirstBit\Appointment\Config\Constants;
use FirstBit\Appointment\Services\OneCReader;
use FirstBit\Appointment\Services\OneCWriter;
use FirstBit\Appointment\Services\Operation\AppointmentOperation;

class OneCController extends Controller
{
    private OneCReader $reader;
    private OneCWriter $writer;

    /**
     * OneCController constructor.
     * @throws \Bitrix\Main\ObjectNotFoundException
     */
    public function __construct()
    {
        parent::__construct();

        $serviceLocator = ServiceLocator::getInstance();

        if ($serviceLocator->has(Constants::ONE_C_READER_SERVICE_ID))
        {
            $this->reader = $serviceLocator->get(Constants::ONE_C_READER_SERVICE_ID);
        }

        if ($serviceLocator->has(Constants::ONE_C_WRITER_SERVICE_ID))
        {
            $this->writer = $serviceLocator->get(Constants::ONE_C_WRITER_SERVICE_ID);
        }
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
        return AppointmentOperation::addOrder($this->writer, $arParams);
    }

    public function deleteOrderAction(int $id, string $orderUid): Result
    {
        return AppointmentOperation::deleteOrder($this->writer, $id, $orderUid);
    }

    public function getOrderStatusAction(int $id, string $orderUid): Result
    {
        return AppointmentOperation::getOrderStatus($this->reader, $id, $orderUid);
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
        return null;
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