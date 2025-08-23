<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.07.2022
 * ==================================================
*/
namespace ANZ\Appointment\Component\Appointment;

use ANZ\Appointment\Component\BaseComponent;
use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Config\Constants;
use ANZ\Appointment\Service\Container;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Exception;

class Add extends BaseComponent
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

        if (!Container::getInstance()->getUserPermissions()->checkReadPermissions())
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
     * @throws \Exception
     */
    public function getAppointmentOptions(): array
    {
        $timeStepDuration = Option::get(
            Configuration::getModuleId(),
            Constants::OPTION_KEY_TIME_STEP_DURATION,
            15
        );
        if (!is_numeric($timeStepDuration)){
            $timeStepDuration = 15;
        }

        return [
            "LOGO_FILE"                       => Option::get(
                Configuration::getModuleId(),
                Constants::OPTION_KEY_LOGO,
            ),
            "USE_CUSTOM_MAIN_BTN"             => Configuration::getInstance()->isCustomBtnEnabled() ? 'Y' : 'N',
            "CUSTOM_MAIN_BTN_ID"              => Configuration::getInstance()->getCustomBtnAttrId(),

            "USE_NOMENCLATURE"                => Configuration::getInstance()->isServicesEnabled(),
            "SELECT_DOCTOR_BEFORE_SERVICE"    => Option::get(
                Configuration::getModuleId(),
                Constants::OPTION_KEY_DOCTOR_BEFORE_SERVICE,
            ),
            "USE_TIME_STEPS"                  => Option::get(
                Configuration::getModuleId(),
                Constants::OPTION_KEY_USE_TIME_STEPS,
            ),

            "TIME_STEP_DURATION"              => $timeStepDuration,

            "STRICT_CHECKING_RELATIONS"       => Option::get(
                Configuration::getModuleId(),
                Constants::OPTION_KEY_STRICT_RELATIONS,
            ),
            "SHOW_DOCTORS_WITHOUT_DEPARTMENT" => Option::get(
                Configuration::getModuleId(),
                Constants::OPTION_KEY_ALLOW_DOCTOR_WITHOUT_DPT,
            ),
            "USE_CONFIRM_WITH"                => Configuration::getInstance()->getExchangeConfirmMode(),
            "USE_EMAIL_NOTE"                  => Option::get(
                Configuration::getModuleId(),
                Constants::OPTION_KEY_EMAIL_NOTE,
            ),
            "PRIVACY_PAGE_URL"                => Option::get(
                Configuration::getModuleId(),
                Constants::OPTION_KEY_PRIVACY_PAGE,
                "javascript: void(0)"
            ),

            "CUSTOM_COLORS" => [
                Constants::OPTION_KEY_MAIN_BTN_TEXT_CLR => Option::get(
                    Configuration::getModuleId(),
                    Constants::OPTION_KEY_MAIN_BTN_TEXT_CLR,
                    "#ffffff"
                ),
                Constants::OPTION_KEY_MAIN_BTN_BG => Option::get(
                    Configuration::getModuleId(),
                    Constants::OPTION_KEY_MAIN_BTN_BG,
                    "#025ea1"
                ),
                Constants::OPTION_KEY_FORM_BG => Option::get(
                    Configuration::getModuleId(),
                    Constants::OPTION_KEY_FORM_BG,
                    "#025ea1"
                ),
                Constants::OPTION_KEY_FIELD_BG => Option::get(
                    Configuration::getModuleId(),
                    Constants::OPTION_KEY_FIELD_BG,
                    "#1B3257"
                ),
                Constants::OPTION_KEY_FORM_TEXT_CLR => Option::get(
                    Configuration::getModuleId(),
                    Constants::OPTION_KEY_FORM_TEXT_CLR,
                    "#f5f5f5"
                ),
                Constants::OPTION_KEY_FORM_BTN_BG => Option::get(
                    Configuration::getModuleId(),
                    Constants::OPTION_KEY_FORM_BTN_BG,
                    "#12b1e3"
                ),
                Constants::OPTION_KEY_FORM_BTN_TEXT_CLR => Option::get(
                    Configuration::getModuleId(),
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