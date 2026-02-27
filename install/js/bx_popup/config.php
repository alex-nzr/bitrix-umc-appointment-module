<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
    die();
}

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Config\ConfirmationType;
use Bitrix\Main\Localization\Loc;

try
{
    $settings = [
        "companyLogo"      => Configuration::getInstance()->getLogoFilePath(),
        "useCustomMainBtn" => Configuration::getInstance()->isCustomBtnEnabled() ? 'Y' : 'N',
        "customMainBtnSelector"  => Configuration::getInstance()->getCustomBtnSelector(),
        "customColors"     => Configuration::getInstance()->getTemplateColors(),

        "useServices"                   => Configuration::getInstance()->isServicesEnabled() ? 'Y' : 'N',
        "useTimeSteps"                  => Configuration::getInstance()->isCustomTimeStepsEnabled() ? 'Y' : 'N',
        "timeStepDurationMinutes"       => Configuration::getInstance()->getCustomTimeStepDurationMinutes(),
        "strictCheckingOfRelations"     => Configuration::getInstance()->isStrictCheckingRelationsEnabled() ? 'Y' : 'N',
        "showDoctorsWithoutDepartment"  => Configuration::getInstance()->isDoctorsWithoutDepartmentShowEnabled() ? 'Y' : 'N',
        "useEmailNote"                  => Configuration::getInstance()->isEmailNotificationEnabled() ? 'Y' : 'N',
        "confirmTypes"                  => [
            'phone'   => ConfirmationType::PHONE->value,
            'email'   => ConfirmationType::EMAIL->value,
            'none'    => ConfirmationType::NONE->value,
        ],
        "useConfirmWith"    => Configuration::getInstance()->getExchangeConfirmMode(),
        "privacyPageLink"   => Configuration::getInstance()->getPrivacyPageLink(),

        "textBlocks" => [
            [
                "type"          => "text",
                "placeholder"   => Loc::getMessage('ANZ_APPOINTMENT_FIELD_NAME_PLACEHOLDER'),
                "id"            => "appointment-form-name",
                "maxlength"     => "30",
                "class"         => "appointment-form_input",
                "name"          => "name",
                "data-required" => "true"
            ],
            [
                "type"          => "text",
                "placeholder"   => Loc::getMessage('ANZ_APPOINTMENT_FIELD_MIDDLE_NAME_PLACEHOLDER'),
                "id"            => "appointment-form-middleName",
                "maxlength"     => "30",
                "class"         => "appointment-form_input",
                "name"          => "middleName",
                "data-required" =>  "true"
            ],
            [
                "type"          => "text",
                "placeholder"   => Loc::getMessage('ANZ_APPOINTMENT_FIELD_LAST_NAME_PLACEHOLDER'),
                "id"            => "appointment-form-surname",
                "maxlength"     => "30",
                "class"         => "appointment-form_input",
                "name"          => "surname",
                "data-required" =>  "true"
            ],
            [
                "type"          => "tel",
                "placeholder"   => Loc::getMessage('ANZ_APPOINTMENT_FIELD_PHONE_PLACEHOLDER'),
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
                "placeholder"   => Loc::getMessage('ANZ_APPOINTMENT_FIELD_BIRTHDAY_PLACEHOLDER'),
                "id"            => "appointment-form-birthday",
                "maxlength"     => "10",
                "class"         => "appointment-form_input",
                "name"          => "birthday",
                "autocomplete"  => "new-password",
                "data-required" => "false",
            ],
            [
                "placeholder"   => Loc::getMessage('ANZ_APPOINTMENT_FIELD_COMMENT_PLACEHOLDER'),
                "id"            => "appointment-form-comment",
                "maxlength"     => "300",
                "class"         => "appointment-form_textarea",
                "name"          => "comment",
                "data-required" => "false"
            ]
        ]
    ];
}
catch (Throwable $e)
{
    $settings = [
        'error' => $e->getMessage()
    ];
}

return [
	'css' => 'dist/classic_form.bundle.css',
	'js'  => 'dist/classic_form.bundle.js',
	'rel' => [
		'date',
		'ui.dialogs.messagebox',
		'main.core',
	],
	'skip_core' => false,
    'lang' => ['lang/ru/lang.php'],
    'settings' => $settings
];