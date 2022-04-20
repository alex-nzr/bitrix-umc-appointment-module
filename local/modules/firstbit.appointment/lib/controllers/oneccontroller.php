<?php
namespace FirstBit\Appointment\Controllers;

use Bitrix\Main\Config\Option;
use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Engine\Action;
use Bitrix\Main\Engine\ActionFilter\Authentication;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use FirstBit\Appointment\Config\Constants;
use FirstBit\Appointment\Services\OneCReader;
use FirstBit\Appointment\Services\OneCWriter;
use FirstBit\Appointment\Services\RecordTableHelper;
use Exception;

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

        if ($serviceLocator->has('appointment.OneCReader'))
        {
            $this->reader = $serviceLocator->get('appointment.OneCReader');
        }

        if ($serviceLocator->has('appointment.OneCWriter'))
        {
            $this->writer = $serviceLocator->get('appointment.OneCWriter');
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

        $useWaitingList = Option::get(
            Constants::APPOINTMENT_MODULE_ID,
            'appointment_settings_use_waiting_list', "N"
        );

        if ($useWaitingList === "Y"){
            $response = $this->writer->addWaitingList($arParams);
        }
        else{
            $response = $this->writer->addOrder($arParams);
        }

        return $response;
    }

    public function deleteOrderAction(int $id, string $orderUid): Result
    {
        try
        {
            $response = $this->writer->deleteOrder($orderUid);
            if ($response->isSuccess())
            {
                $ormRes = RecordTableHelper::deleteRecord($id);
                $data = $response->getData();
                $response->setData(array_merge($data, $ormRes));
                return $response;
            }
            else
            {
                throw new Exception(implode(", ", $response->getErrorMessages()));
            }
        }
        catch (Exception $e)
        {
            return (new Result)->addError(new Error($e->getMessage()));
        }
    }

    public function getOrderStatusAction(int $id, string $orderUid): Result
    {
        try
        {
            $response = $this->reader->getOrderStatus($orderUid);
            if ($response->isSuccess())
            {
                $data = $response->getData();
                $status = $data['status'] ?? "-";
                $ormRes = RecordTableHelper::updateRecord($id, ['STATUS_1C' => $status]);
                $response->setData(array_merge($data, $ormRes));
                return $response;
            }
            else
            {
                throw new Exception(implode(", ", $response->getErrorMessages()));
            }
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