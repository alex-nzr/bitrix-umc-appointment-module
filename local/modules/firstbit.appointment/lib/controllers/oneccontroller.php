<?php
namespace FirstBit\Appointment\Controllers;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Error;
use FirstBit\Appointment\Services\OneCReader;
use FirstBit\Appointment\Services\OneCWriter;

class OneCController extends Controller
{
    private OneCReader $reader;
    private OneCWriter $writer;

    public function __construct(/*OneCReader $reader, OneCWriter $writer*/)
    {
        parent::__construct();
        //$this->reader = $reader;
        //$this->writer = $writer;
        $this->reader = new OneCReader();
        $this->writer = new OneCWriter();
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

    public function getNomenclatureAction($clinicGuid): ?array
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

    public function addOrderAction($params): ?array
    {
        $response = $this->writer->addOrder($params);
        if ($response['error']){
            $this->addError(new Error($response['error']));
            return null;
        }
        return $response;
    }

    public function configureActions(): array
    {
        return [
            'getById'   => [ 'prefilters' => [], 'postfilters' => [] ],
            'add'       => [ 'prefilters' => [], 'postfilters' => [] ],
            'update'    => [ 'prefilters' => [], 'postfilters' => [] ],
            'delete'    => [ 'prefilters' => [], 'postfilters' => [] ]
        ];
    }
}