<?php
namespace FirstBit\Appointment\Component;

use Bitrix\Main\Engine\ActionFilter\Authentication;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Engine\Controller;
use CBitrixComponent;
use CFile;
use FirstBit\Appointment\Config\Constants;

/**
 * Class AppFormAjaxController
 * @package FirstBit\Appointment\Component
 */
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
            "companyLogo"      => $params['LOGO_FILE'] > 0 ? CFile::GetPath($params['LOGO_FILE']) : '',
            "useCustomMainBtn" => $params['USE_CUSTOM_MAIN_BTN'],
            "customMainBtnId"  => $params['CUSTOM_MAIN_BTN_ID'],
            "customColors"     => $params["CUSTOM_COLORS"],

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
            "useConfirmWith"    => $params["USE_CONFIRM_WITH"],
            "privacyPageLink"   => $params["PRIVACY_PAGE_URL"],

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
                    "autocomplete"  => "new-password",
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

    public function configureActions(): array
    {
        return [
            'getResult'     => [
                'prefilters' => [
                    new HttpMethod([HttpMethod::METHOD_POST]),
                    new Csrf(),
                ],
            ],
        ];
    }
}