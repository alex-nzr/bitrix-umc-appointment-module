<?php
namespace FirstBit\Appointment\Controllers;

use Bitrix\Main\Config\Option;
use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Engine\ActionFilter\Authentication;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Error;
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


    public function getClinicsAction(): ?array
    {

        $response = $this->reader->getClinicsList();
        if ($response['error']){
            $this->addError(new Error($response['error']));
            return null;
        }
        return $response;
    }

    public function getEmployeesAction(): ?array
    {
        $response = $this->reader->getEmployeesList();
        if ($response['error']){
            $this->addError(new Error($response['error']));
            return null;
        }
        return $response;
    }

    public function getNomenclatureAction(string $clinicGuid): ?array
    {
        $response = $this->reader->getNomenclatureList($clinicGuid);
        if ($response['error']){
            $this->addError(new Error($response['error']));
            return null;
        }
        return $response;
    }

    public function getScheduleAction(): ?array
    {
        $response = $this->reader->getSchedule();
        if ($response['error']){
            $this->addError(new Error($response['error']));
            return null;
        }
        return $response;
    }

    public function addOrderAction(string $params): ?array
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

        if ($response['error']){
            $this->addError(new Error($response['error']));
            return null;
        }
        return $response;
    }

    public function deleteOrderAction(int $id, string $orderUid): ?array
    {
        try
        {
            $response = $this->writer->deleteOrder($orderUid);
            if ($response['error'])
            {
                throw new Exception($response['error']);
            }
            else
            {
                $ormRes = RecordTableHelper::deleteRecord($id);
                return array_merge($response, $ormRes);
            }
        }
        catch (Exception $e)
        {
            $this->addError(new Error($e->getMessage()));
            return null;
        }
    }

    public function getOrderStatusAction(int $id, string $orderUid): ?array
    {
        try
        {
            $response = $this->reader->getOrderStatus($orderUid);
            if ($response['error'])
            {
                throw new Exception($response['error']);
            }
            else
            {
                $status = $response['status'] ?? "-";
                $ormRes = RecordTableHelper::updateRecord($id, ['STATUS_1C' => $status]);
                return array_merge($response, $ormRes);
            }
        }
        catch (Exception $e)
        {
            $this->addError(new Error($e->getMessage()));
            return null;
        }
    }

    protected function getDefaultPreFilters(): array
    {
        return [
            new HttpMethod([HttpMethod::METHOD_POST])
        ];
    }

    public function configureActions(): array
    {
        return [
            'deleteOrder'     => [
                '+prefilters' => [
                    new Authentication(),
                    new Csrf(),
                ],
            ],
        ];
    }
}