<?php
namespace FirstBit\Appointment\Component;

use Bitrix\Main\Engine\Controller;
use CBitrixComponent;
use FirstBit\Appointment\Config\Constants;

class AppFormAjaxController extends Controller
{
    public function getResultAction(): array
    {
        $componentResult = $this->getComponentResult();
        return $this->getPreparedTemplateParameters($componentResult);
    }

    /**
     * @return array
     */
    protected function getComponentResult():array
    {
        /** @var \FirstBit\Appointment\Component\AppForm $class */
        $class = CBitrixComponent::includeComponentClass('firstbit:appointment.add');
        return (new $class)->getResult();
    }

    /**
     * @param array $params
     * @return array
     */
    protected function getPreparedTemplateParameters(array $params): array
    {
        return [
            "useCustomMainBtn" => $params['USE_CUSTOM_MAIN_BTN'],
            "customMainBtnId"  => $params['CUSTOM_MAIN_BTN_ID'],
            "customColors"     => $params["CUSTOM_COLORS"],

            "ajaxUrl"                       => "/bitrix/services/main/ajax.php",
            "useServices"                   => $params["USE_NOMENCLATURE"],
            "selectDoctorBeforeService"     => $params["SELECT_DOCTOR_BEFORE_SERVICE"],
            "useTimeSteps"                  => $params["USE_TIME_STEPS"],
            "timeStepDurationMinutes"       => $params["TIME_STEP_DURATION"],
            "strictCheckingOfRelations"     => $params["STRICT_CHECKING_RELATIONS"],
            "showDoctorsWithoutDepartment"  => $params["SHOW_DOCTORS_WITHOUT_DEPARTMENT"],
            "useEmailNote"                  => $params["USE_EMAIL_NOTE"],
            "confirmTypes"                  => [
                'phone'   => Constants::CONFIRM_TYPE_PHONE,
                'email'   => Constants::CONFIRM_TYPE_EMAIL,
                'none'    => Constants::CONFIRM_TYPE_NONE,
            ],
            "useConfirmWith"                => $params["USE_CONFIRM_WITH"],
            "privacyPageLink"               => $params["PRIVACY_PAGE_URL"],

            "widgetBtnWrapId"   =>  "appointment-button-wrapper",
            "wrapperId"         =>        "appointment-widget-wrapper",
            "formId"            =>           "appointment-form",
            "widgetBtnId"       =>      "appointment-button",
            "messageNodeId"     =>    "appointment-form-message",
            "submitBtnId"       =>      "appointment-form-button",
            "appResultBlockId"  => "appointment-result-block",

            "selectionNodes" => [],
            "defaultText"    => [],
            "textNodes"      => [],
            "isUpdate"       => false,
            "dataKeys"       => [
                "clinicsKey"     => $params["CLINICS_KEY"],
                "specialtiesKey" => $params["SPECIALTIES_KEY"],
                "servicesKey"    => $params["SERVICES_KEY"],
                "employeesKey"   => $params["EMPLOYEES_KEY"],
                "scheduleKey"    => $params["SCHEDULE_KEY"],
            ],

            "selectionBlocks" => [
                "clinicsBlock"      => [
                    "id"    => $params["CLINICS_KEY"],
                    "name"  => "Выберите клинику"
                ],
                "specialtiesBlock"  => [
                    "id"    => $params["SPECIALTIES_KEY"],
                    "name"  => "Выберите направление"
                ],
                "servicesBlock"     => [
                    "id"    => $params["SERVICES_KEY"],
                    "name"  => "Выберите услугу"
                ],
                "employeesBlock"    => [
                    "id"    => $params["EMPLOYEES_KEY"],
                    "name"  => "Выберите врача"
                ],
                "scheduleBlock"     => [
                    "id"    => $params["SCHEDULE_KEY"],
                    "name"  => "Выберите время"
                ]
            ],
            "textBlocks" => [
                [
                    "type"          => "text",
                    "placeholder"   => "Имя *",
                    "id"            => "appointment-form-name",
                    "maxlength"     => "30",
                    "class"         => "appointment-form_input",
                    "name"          => "name",
                    "data-required" => "true"
                ],
                [
                    "type"          => "text",
                    "placeholder"   => "Отчество *",
                    "id"            => "appointment-form-middleName",
                    "maxlength"     => "30",
                    "class"         => "appointment-form_input",
                    "name"          => "middleName",
                    "data-required" =>  "true"
                ],
                [
                    "type"          => "text",
                    "placeholder"   => "Фамилия *",
                    "id"            => "appointment-form-surname",
                    "maxlength"     => "30",
                    "class"         => "appointment-form_input",
                    "name"          => "surname",
                    "data-required" =>  "true"
                ],
                [
                    "type"          => "tel",
                    "placeholder"   => "Телефон *",
                    "id"            => "appointment-form-phone",
                    "maxlength"     => "30",
                    "class"         => "appointment-form_input",
                    "name"          => "phone",
                    "data-required" =>  "true",
                    "autocomplete"  => "new-password",
                    "aria-autocomplete" => "list"
                ],
                [
                    "type"          => "email",
                    "placeholder"   => "Email",
                    "id"            => "appointment-form-email",
                    "maxlength"     => "50",
                    "class"         => "appointment-form_input",
                    "name"          => "email",
                    "data-required" => "false"
                ],
                [
                    "type"          => "text",
                    "placeholder"   => "День рождения",
                    "id"            => "appointment-form-birthday",
                    "maxlength"     => "10",
                    "class"         => "appointment-form_input",
                    "name"          => "birthday",
                    "data-required" => "false",
                ],
                [
                    "placeholder"   => "Комментарий",
                    "id"            => "appointment-form-comment",
                    "maxlength"     => "300",
                    "class"         => "appointment-form_textarea",
                    "name"          => "comment",
                    "data-required" => "false"
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function configureActions(): array
    {
        return [];
    }
}