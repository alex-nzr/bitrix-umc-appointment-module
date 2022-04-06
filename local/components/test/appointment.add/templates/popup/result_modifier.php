<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * @var array $arResult
 * @var array $arParams
 */


    $arResult["JS_SETTINGS"] = [
        "ajaxUrl"                       => "/bitrix/services/main/ajax.php",
        "useServices"                   => $arResult["USE_NOMENCLATURE"],
        "selectDoctorBeforeService"     => $arResult["SELECT_DOCTOR_BEFORE_SERVICE"],
        "useTimeSteps"                  => $arResult["USE_TIME_STEPS"],
        "timeStepDurationMinutes"       => $arResult["TIME_STEP_DURATION"],
        "strictCheckingOfRelations"     => $arResult["STRICT_CHECKING_RELATIONS"],
        "showDoctorsWithoutDepartment"  => $arResult["SHOW_DOCTORS_WITHOUT_DEPARTMENT"],
        "privacyPageLink"               => $arResult["PRIVACY_PAGE_URL"],

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
            "clinicsKey"     => $arResult["CLINICS_KEY"],
            "specialtiesKey" => $arResult["SPECIALTIES_KEY"],
            "servicesKey"    => $arResult["SERVICES_KEY"],
            "employeesKey"   => $arResult["EMPLOYEES_KEY"],
            "scheduleKey"    => $arResult["SCHEDULE_KEY"],
        ],

        "selectionBlocks" => [
            "clinicsBlock"      => [
                "id"    => $arResult["CLINICS_KEY"],
                "name"  => "Выберите клинику"
            ],
            "specialtiesBlock"  => [
                "id"    => $arResult["SPECIALTIES_KEY"],
                "name"  => "Выберите направление"
            ],
            "servicesBlock"     => [
                "id"    => $arResult["SERVICES_KEY"],
                "name"  => "Выберите услугу"
            ],
            "employeesBlock"    => [
                "id"    => $arResult["EMPLOYEES_KEY"],
                "name"  => "Выберите врача"
            ],
            "scheduleBlock"     => [
                "id"    => $arResult["SCHEDULE_KEY"],
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
                "placeholder"   => "Email *",
                "id"            => "appointment-form-email",
                "maxlength"     => "50",
                "class"         => "appointment-form_input",
                "name"          => "email",
                "data-required" =>  "false"
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