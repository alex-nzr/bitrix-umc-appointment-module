<?php
namespace FirstBit\Appointment\Services;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Result;
use Exception;
use FirstBit\Appointment\Config\Constants;
use FirstBit\Appointment\Utils\Utils;

Loc::loadMessages(__FILE__);

class OneCReader extends BaseOneCService
{
    public function getClinicsList(): Result
    {
        if (Constants::DEMO_MODE === "Y"){
            $res = new Result();
            sleep(3);
            try {
                $res->setData($this->demoData['clinics']);
            }catch (Exception $e){
                $res->addError(new Error(Loc::getMessage("FIRSTBIT_APPOINTMENT_DEMO_MODE_ERROR") . $e->getMessage()));
            }
            return $res;
        }

        return $this->send(Constants::CLINIC_ACTION_1C);
    }

    public function getEmployeesList(): Result
    {
        if (Constants::DEMO_MODE === "Y"){
            $res = new Result();
            sleep(3);
            try {
                $res->setData($this->demoData['employees']);
            }catch (Exception $e){
                $res->addError(new Error(Loc::getMessage("FIRSTBIT_APPOINTMENT_DEMO_MODE_ERROR") . $e->getMessage()));
            }
            return $res;
        }

        return $this->send(Constants::EMPLOYEES_ACTION_1C);
    }

    public function getNomenclatureList($clinicGuid): Result
    {
        if (Constants::DEMO_MODE === "Y"){
            $res = new Result();
            sleep(3);
            try {
                $res->setData($this->demoData['nomenclature']);
            }catch (Exception $e){
                $res->addError(new Error(Loc::getMessage("FIRSTBIT_APPOINTMENT_DEMO_MODE_ERROR") . $e->getMessage()));
            }
            return $res;
        }
        $params = [
            'Clinic' => $clinicGuid,
            'Params' => []
        ];
        return $this->send(Constants::NOMENCLATURE_ACTION_1C, $params);
    }

    public function getSchedule(): Result
    {
        if (Constants::DEMO_MODE === "Y"){
            $res = new Result();
            sleep(3);
            try {
                $res->setData($this->demoData['schedule']);
            }catch (Exception $e){
                $res->addError(new Error(Loc::getMessage("FIRSTBIT_APPOINTMENT_DEMO_MODE_ERROR") . $e->getMessage()));
            }
            return $res;
        }

        $period = Utils::getDateInterval(
            Option::get(
                Constants::APPOINTMENT_MODULE_ID,
                "appointment_api_schedule_days",
                Constants::DEFAULT_SCHEDULE_PERIOD_DAYS
            )
        );

        return $this->send(Constants::SCHEDULE_ACTION_1C, $period);
    }

    /** get order status from 1C
     * @param string $orderUid
     * @return \Bitrix\Main\Result
     */
    public function getOrderStatus(string $orderUid): Result
    {
        return $this->send(Constants::GET_ORDER_STATUS_ACTION_1C, ['GUID' => $orderUid]);
    }
}