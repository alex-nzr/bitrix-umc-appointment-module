<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * Bit.Umc - Bitrix integration - RecordAdd.php
 * 14.02.2023 15:03
 * ==================================================
 */
namespace ANZ\Appointment\Component;

use ANZ\Appointment\Config\Constants;
use ANZ\Appointment\Internals\Control\ServiceManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Exception;

/**
 * @class RecordAdd
 * @package ANZ\Appointment\Component
 */
class RecordAdd extends BaseComponent
{
    /**
     * @return bool
     * @throws \Exception
     */
    function checkRequirements(): bool
    {
        if (!Loader::includeModule('anz.appointment'))
        {
            throw new Exception("Can not include '$this->moduleId' module");
        }

        if ($this->App->GetGroupRight(ServiceManager::getModuleId()) < "R")
        {
            throw new Exception('Access to component denied');
        }

        return true;
    }

    /**
     * @return array
     */
    public function getResult(): array
    {
        try
        {
            $templateOptions = $this->getAppointmentOptions();
            $templateKeys = $this->getTemplateKeys();

            return array_merge(
                $templateKeys,
                $templateOptions
            );
        }
        catch(Exception $e){
            $this->showMessage($e->getMessage(), true);
            return [];
        }
    }

    /**
     * @return array
     */
    public function getAppointmentOptions(): array
    {
        $timeStepDuration = Option::get(
            ServiceManager::getModuleId(),
            Constants::OPTION_KEY_TIME_STEP_DURATION,
            15
        );
        if (!is_numeric($timeStepDuration)){
            $timeStepDuration = 15;
        }

        return [
            "LOGO_FILE"                       => Option::get(
                ServiceManager::getModuleId(),
                Constants::OPTION_KEY_LOGO,
            ),
            "USE_CUSTOM_MAIN_BTN"             => Option::get(
                ServiceManager::getModuleId(),
                Constants::OPTION_KEY_USE_CUSTOM_BTN,
            ),
            "CUSTOM_MAIN_BTN_ID"              => Option::get(
                ServiceManager::getModuleId(),
                Constants::OPTION_KEY_CUSTOM_BTN_ID
            ),

            "USE_NOMENCLATURE"                => Option::get(
                ServiceManager::getModuleId(),
                Constants::OPTION_KEY_USE_NOMENCLATURE,
            ),
            "SELECT_DOCTOR_BEFORE_SERVICE"    => Option::get(
                ServiceManager::getModuleId(),
                Constants::OPTION_KEY_DOCTOR_BEFORE_SERVICE,
            ),
            "USE_TIME_STEPS"                  => Option::get(
                ServiceManager::getModuleId(),
                Constants::OPTION_KEY_USE_TIME_STEPS,
            ),

            "TIME_STEP_DURATION"              => $timeStepDuration,

            "STRICT_CHECKING_RELATIONS"       => Option::get(
                ServiceManager::getModuleId(),
                Constants::OPTION_KEY_STRICT_RELATIONS,
            ),
            "SHOW_DOCTORS_WITHOUT_DEPARTMENT" => Option::get(
                ServiceManager::getModuleId(),
                Constants::OPTION_KEY_ALLOW_DOCTOR_WITHOUT_DPT,
            ),
            "USE_CONFIRM_WITH"                => Option::get(
                ServiceManager::getModuleId(),
                Constants::OPTION_KEY_CONFIRM_MODE,
                "none"
            ),
            "USE_EMAIL_NOTE"                  => Option::get(
                ServiceManager::getModuleId(),
                Constants::OPTION_KEY_EMAIL_NOTE,
            ),
            "PRIVACY_PAGE_URL"                => Option::get(
                ServiceManager::getModuleId(),
                Constants::OPTION_KEY_PRIVACY_PAGE,
                "javascript: void(0)"
            ),

            "CUSTOM_COLORS" => [
                Constants::OPTION_KEY_MAIN_BTN_TEXT_CLR => Option::get(
                    ServiceManager::getModuleId(),
                    Constants::OPTION_KEY_MAIN_BTN_TEXT_CLR,
                    "#ffffff"
                ),
                Constants::OPTION_KEY_MAIN_BTN_BG => Option::get(
                    ServiceManager::getModuleId(),
                    Constants::OPTION_KEY_MAIN_BTN_BG,
                    "#025ea1"
                ),
                Constants::OPTION_KEY_FORM_BG => Option::get(
                    ServiceManager::getModuleId(),
                    Constants::OPTION_KEY_FORM_BG,
                    "#025ea1"
                ),
                Constants::OPTION_KEY_FIELD_BG => Option::get(
                    ServiceManager::getModuleId(),
                    Constants::OPTION_KEY_FIELD_BG,
                    "#1B3257"
                ),
                Constants::OPTION_KEY_FORM_TEXT_CLR => Option::get(
                    ServiceManager::getModuleId(),
                    Constants::OPTION_KEY_FORM_TEXT_CLR,
                    "#f5f5f5"
                ),
                Constants::OPTION_KEY_FORM_BTN_BG => Option::get(
                    ServiceManager::getModuleId(),
                    Constants::OPTION_KEY_FORM_BTN_BG,
                    "#12b1e3"
                ),
                Constants::OPTION_KEY_FORM_BTN_TEXT_CLR => Option::get(
                    ServiceManager::getModuleId(),
                    Constants::OPTION_KEY_FORM_BTN_TEXT_CLR,
                    "#ffffff"
                ),
            ]
        ];
    }

    /**
     * @return string[]
     */
    public function getTemplateKeys(): array
    {
        return [
            "CLINICS_KEY"     => "anz_appointment_clinics",
            "SPECIALTIES_KEY" => "anz_appointment_specialties",
            "SERVICES_KEY"    => "anz_appointment_services",
            "EMPLOYEES_KEY"   => "anz_appointment_employees",
            "SCHEDULE_KEY"    => "anz_appointment_schedule",
        ];
    }
}