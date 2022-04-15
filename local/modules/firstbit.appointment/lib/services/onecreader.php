<?php
namespace FirstBit\Appointment\Services;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Exception;
use FirstBit\Appointment\Config\Constants;
use FirstBit\Appointment\Utils\Utils;

Loc::loadMessages(__FILE__);

class OneCReader extends BaseOneCService
{
    public function getClinicsList(): array
    {
        if (Constants::DEMO_MODE === "Y"){
            sleep(3);
            try {
                return $this->demoData['clinics'];
            }catch (Exception $e){
                return ['error' => Loc::getMessage("FIRSTBIT_APPOINTMENT_DEMO_MODE_ERROR") . $e->getMessage()];
            }
        }

        return $this->send(Constants::CLINIC_ACTION_1C);
    }

    public function getEmployeesList(): array
    {
        if (Constants::DEMO_MODE === "Y"){
            try {
                return $this->demoData['employees'];
            }catch (Exception $e){
                return ['error' => Loc::getMessage("FIRSTBIT_APPOINTMENT_DEMO_MODE_ERROR") . $e->getMessage()];
            }
        }

        return $this->send(Constants::EMPLOYEES_ACTION_1C);
    }

    public function getNomenclatureList($clinicGuid): array
    {
        if (Constants::DEMO_MODE === "Y"){
            try {
                return $this->demoData['nomenclature'];
            }catch (Exception $e){
                return ['error' => Loc::getMessage("FIRSTBIT_APPOINTMENT_DEMO_MODE_ERROR") . $e->getMessage()];
            }
        }
        $params = [
            'Clinic' => $clinicGuid,
            'Params' => []
        ];
        return $this->send(Constants::NOMENCLATURE_ACTION_1C, $params);
    }

    public function getSchedule(): array
    {
        if (Constants::DEMO_MODE === "Y"){
            try {
                return ['schedule' => $this->demoData['schedule']];
            }catch (Exception $e){
                return ['error' => Loc::getMessage("FIRSTBIT_APPOINTMENT_DEMO_MODE_ERROR") . $e->getMessage()];
            }
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
     * @return array
     */
    public function getOrderStatus(string $orderUid): array
    {
        return $this->send(Constants::GET_ORDER_STATUS_ACTION_1C, ['GUID' => $orderUid]);
    }
}