<?php
namespace FirstBit\Appointment\Component;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Config\Option;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Result;
use Bitrix\Main\Type\DateTime;
use CBitrixComponent;
use CMain;
use Exception;
use FirstBit\Appointment\Config\Constants;
use FirstBit\Appointment\Model\RecordTable;

class AppForm extends CBitrixComponent implements Controllerable
{
    private CMain $App;
    private Result $result;

    public function __construct($component = null)
    {
        $this->App = $GLOBALS['APPLICATION'];
        $this->result = new Result();
        parent::__construct($component);
    }

    public function onPrepareComponentParams($arParams): array
    {
        return array_merge($arParams, [
            "CACHE_TYPE" => $arParams["CACHE_TYPE"] ?? "A",
            "CACHE_TIME" => $arParams["CACHE_TIME"] ?? 3600,
        ]);
    }

    public function executeComponent()
    {
        if ($this->checkModules())
        {
            if ($this->App->GetGroupRight(Constants::APPOINTMENT_MODULE_ID) < "R")
            {
                $this->showMessage(Loc::getMessage("FIRSTBIT_APPOINTMENT_COMPONENT_ACCESS_DENIED"), true);
            }
            else
            {
                if ($this->startResultCache($this->arParams['CACHE_TIME']))
                {
                    $this->arResult = $this->getResult();
                    $this->includeComponentTemplate();
                    $this->endResultCache();
                }
            }
        }
    }

    public function getResult(): array
    {
        try {
            $templateOptions = $this->getAppointmentOptions();
            $templateKeys = $this->getTemplateKeys();

            $this->result->setData(array_merge(
                $this->result->getData(),
                $templateKeys,
                $templateOptions
            ));

            if ($this->result->isSuccess()){
                return $this->result->getData();
            }else{
                throw new Exception(implode("; ", $this->result->getErrorMessages()));
            }
        }
        catch(Exception $e){
            $this->showMessage($e->getMessage(), true);
            return [];
        }
    }

    public function getAppointmentOptions(): array
    {
        $timeStepDuration = Option::get(
            Constants::APPOINTMENT_MODULE_ID,
            'appointment_settings_time_step_duration',
            15
        );
        if (!is_numeric($timeStepDuration)){
            $timeStepDuration = 15;
        }

        return [
            "USE_NOMENCLATURE"                => Option::get(
                Constants::APPOINTMENT_MODULE_ID,
                'appointment_settings_use_nomenclature',
                "Y"
            ),
            "SELECT_DOCTOR_BEFORE_SERVICE"    => Option::get(
                Constants::APPOINTMENT_MODULE_ID,
                'appointment_settings_select_doctor_before_service',
                "N"
            ),
            "USE_TIME_STEPS"                  => Option::get(
                Constants::APPOINTMENT_MODULE_ID,
                'appointment_settings_use_time_steps',
                "N"
            ),

            "TIME_STEP_DURATION"              => $timeStepDuration,

            "STRICT_CHECKING_RELATIONS"       => Option::get(
                Constants::APPOINTMENT_MODULE_ID,
                'appointment_settings_strict_checking_relations',
                "Y"
            ),
            "SHOW_DOCTORS_WITHOUT_DEPARTMENT" => Option::get(
                Constants::APPOINTMENT_MODULE_ID,
                'appointment_settings_show_doctors_without_dpt',
                "Y"
            ),
            "PRIVACY_PAGE_URL"                => Option::get(
                Constants::APPOINTMENT_MODULE_ID,
                'appointment_settings_privacy_page_url',
                "javascript: void(0)"
            ),
        ];
    }

    public function getTemplateKeys(): array
    {
        return [
            "CLINICS_KEY"     => "clinics",
            "SPECIALTIES_KEY" => "specialties",
            "SERVICES_KEY"    => "services",
            "EMPLOYEES_KEY"   => "employees",
            "SCHEDULE_KEY"    => "schedule",
        ];
    }

    /**
     * @param array $params
     * @return array
     */
    public function addAction(array $params): array
    {
        try {
            if (!$this->checkModules()){
                throw new Exception(Loc::getMessage("FIRSTBIT_APPOINTMENT_MODULE_NOT_INCLUDED"));
            }

            if (!empty($params['DATETIME_VISIT'])){
                $params['DATETIME_VISIT'] = DateTime::createFromTimestamp(strtotime($params['DATETIME_VISIT']));
            }
            $result = RecordTable::add($params);

            if ($result->isSuccess()){
                return array_merge($result->getData(), ['ID' => $result->getId()]);
            }
            else{
                throw new Exception(implode("; ", $result->getErrorMessages()));
            }
        }
        catch(Exception $e){
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * @param int $id
     * @param array $params
     * @return array
     */
    public function updateAction(int $id, array $params): array
    {
        try {
            if (!$this->checkModules()){
                throw new Exception(Loc::getMessage("FIRSTBIT_APPOINTMENT_MODULE_NOT_INCLUDED"));
            }

            $result = RecordTable::update($id, $params);

            if ($result->isSuccess()){
                return array_merge($result->getData(), ['ID' => $result->getId()]);
            }
            else{
                throw new Exception(implode("; ", $result->getErrorMessages()));
            }
        }
        catch(Exception $e){
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * @param int $id
     * @return array
     */
    public function deleteAction(int $id): array
    {
        try {
            if (!$this->checkModules()){
                throw new Exception(Loc::getMessage("FIRSTBIT_APPOINTMENT_MODULE_NOT_INCLUDED"));
            }

            $result = RecordTable::delete($id);

            if ($result->isSuccess()){
                return array_merge($result->getData(), ['message' => 'deleted with id = '. $id]);
            }
            else{
                throw new Exception(implode("; ", $result->getErrorMessages()));
            }
        }
        catch(Exception $e){
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * @return bool
     */
    protected function checkModules(): bool
    {
        try{
            if (Loader::includeModule('firstbit.appointment')){
                return true;
            }else{
                throw new Exception(Loc::getMessage("FIRSTBIT_APPOINTMENT_MODULE_NOT_INCLUDED"));
            }
        }
        catch(Exception $e){
            $this->AbortResultCache();
            $this->showMessage($e->getMessage(), true);
            return false;
        }
    }

    /**
     * @return array
     */
    public function configureActions(): array
    {
        return [
            'test' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([ActionFilter\HttpMethod::METHOD_POST]),
                    /*new ActionFilter\Authentication(),
                    new ActionFilter\Csrf(),*/
                ],
                'postfilters' => []
            ]
        ];
    }

    /**
     * @param string $message
     * @param bool $isError
     */
    protected function showMessage(string $message, $isError = false): void
    {
        $isError ? ShowError($message) : ShowMessage($message);
    }
}