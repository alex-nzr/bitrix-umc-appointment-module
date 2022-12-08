<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - Reader.php
 * 10.07.2022 22:37
 * ==================================================
 */
namespace ANZ\Appointment\Service\OneC;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Result;
use Exception;
use ANZ\Appointment\Config\Constants;
use ANZ\Appointment\Tools\Utils;
use SoapVar;

Loc::loadMessages(__FILE__);

/**
 * Class Reader
 * @package ANZ\Appointment\Service\OneC
 */
class Reader extends BaseService
{
    public function getClinicsList(): Result
    {
        if (Constants::DEMO_MODE === "Y"){
            $res = new Result();
            sleep(1);
            try {
                $res->setData($this->demoData['clinics']);
            }catch (Exception $e){
                $res->addError(new Error(Loc::getMessage("ANZ_APPOINTMENT_DEMO_MODE_ERROR") . $e->getMessage()));
            }
            return $res;
        }

        return $this->send(Constants::CLINIC_ACTION_1C);
    }

    public function getEmployeesList(): Result
    {
        if (Constants::DEMO_MODE === "Y"){
            $res = new Result();
            sleep(1);
            try {
                $res->setData($this->demoData['employees']);
            }catch (Exception $e){
                $res->addError(new Error(Loc::getMessage("ANZ_APPOINTMENT_DEMO_MODE_ERROR") . $e->getMessage()));
            }
            return $res;
        }

        return $this->send(Constants::EMPLOYEES_ACTION_1C);
    }

    public function getNomenclatureList($clinicGuid): Result
    {
        if (Constants::DEMO_MODE === "Y"){
            $res = new Result();
            sleep(1);
            try {
                $res->setData($this->demoData['services']);
            }catch (Exception $e){
                $res->addError(new Error(Loc::getMessage("ANZ_APPOINTMENT_DEMO_MODE_ERROR") . $e->getMessage()));
            }
            return $res;
        }
        $params = [
            'Clinic' => $clinicGuid,
            'Params' => []
        ];
        return $this->send(Constants::NOMENCLATURE_ACTION_1C, $params);
    }

    public function getSchedule(array $params = []): Result
    {
        if (Constants::DEMO_MODE === "Y"){
            $res = new Result();
            sleep(1);
            try {
                $res->setData(['schedule' => $this->demoData['schedule']]);
            }catch (Exception $e){
                $res->addError(new Error(Loc::getMessage("ANZ_APPOINTMENT_DEMO_MODE_ERROR") . $e->getMessage()));
            }
            return $res;
        }

        $soapParams = Utils::getDateInterval(
            Option::get(
                Constants::APPOINTMENT_MODULE_ID,
                "appointment_api_schedule_days",
                Constants::DEFAULT_SCHEDULE_PERIOD_DAYS
            )
        );

        $properties = [];

        if (!empty($params['clinicUid']))
        {
            $properties[] = new SoapVar(
                '<ns2:Property name="Clinic"><ns2:Value>'.$params['clinicUid'].'</ns2:Value></ns2:Property>',
                XSD_ANYXML
            );
        }

        if (is_array($params['employees']) && !empty($params['employees']))
        {
            $properties[] = new SoapVar(
                '<ns2:Property name="Employees"><ns2:Value>'.implode(';', $params['employees']).'</ns2:Value></ns2:Property>',
                XSD_ANYXML
            );
        }

        $soapParams['Params'] = $properties;

        return $this->send(Constants::SCHEDULE_ACTION_1C, $soapParams);
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