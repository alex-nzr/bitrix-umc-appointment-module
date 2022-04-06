<?php
namespace FirstBit\Appointment\Controllers;

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Engine\ActionFilter\Authentication;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Error;
use FirstBit\Appointment\Services\OneCReader;
use FirstBit\Appointment\Services\OneCWriter;
use FirstBit\Appointment\Utils\Utils;

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
        $response = $this->writer->addOrder($arParams);
        if ($response['error']){
            $this->addError(new Error($response['error']));
            return null;
        }
        return $response;
    }

    public function configureActions(): array
    {
        return [
            'getClinics'        => [ 'prefilters' => [], 'postfilters' => [] ],
            'getEmployees'      => [ 'prefilters' => [], 'postfilters' => [] ],
            'getNomenclature'   => [ 'prefilters' => [], 'postfilters' => [] ],
            'getSchedule'       => [ 'prefilters' => [], 'postfilters' => [] ],
            'addOrder'          => [ 'prefilters' => [], 'postfilters' => [] ],
            'customTest'        => [ '-prefilters' => [ new Authentication() ]
            ],
        ];
    }
}