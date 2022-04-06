<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * @var array $arResult
 * @var array $arParams
 */

CJSCore::Init(['ajax']);

$selectionBlocks = [
    $arResult["CLINICS_KEY"]     => "Выберите клинику",
    $arResult["SPECIALTIES_KEY"] => "Выберите специализацию",
];
if ($arResult["SELECT_DOCTOR_BEFORE_SERVICE"] === "Y")
{
    $selectionBlocks[$arResult["EMPLOYEES_KEY"]] = "Выберите врача";
    $selectionBlocks[$arResult["SERVICES_KEY"] ]  = "Выберите услугу";
}
else{
    $selectionBlocks[$arResult["SERVICES_KEY"] ]  = "Выберите услугу";
    $selectionBlocks[$arResult["EMPLOYEES_KEY"]] = "Выберите врача";
}
$selectionBlocks[$arResult["SCHEDULE_KEY"]] = "Выберите время";

$textBlocks = [
    [
        "type" => "text",
        "placeholder" => "Имя *",
        "id" => "appointment-form-name",
        "maxlength" => "30",
        "class" => "appointment-form_input",
        "name" => "name",
    ],
    [
        "type" => "text",
        "placeholder" => "Отчество *",
        "id" => "appointment-form-middleName",
        "maxlength" => "30",
        "class" => "appointment-form_input",
        "name" => "middleName",
    ],
    [
        "type" => "text",
        "placeholder" => "Фамилия *",
        "id" => "appointment-form-surname",
        "maxlength" => "30",
        "class" => "appointment-form_input",
        "name" => "surname",
    ],
    [
        "type" => "tel",
        "placeholder" => "Телефон *",
        "id" => "appointment-form-phone",
        "maxlength" => "30",
        "class" => "appointment-form_input",
        "name" => "phone",
        "autocomplete" => "new-password",
        "aria-autocomplete" => "list"
    ],
    [
        "placeholder" => "Комментарий",
        "id" => "appointment-form-comment",
        "maxlength" => "300",
        "class" => "appointment-form_textarea",
        "name" => "comment",
    ]
];

$arResult["SELECTION_BLOCKS"] = $selectionBlocks;
$arResult["TEXT_BLOCKS"]      = $textBlocks;