<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$arComponentDescription = [
    "NAME" => Loc::getMessage("FIRSTBIT_APPOINTMENT_COMPONENT_NAME"),
    "DESCRIPTION" => Loc::getMessage("FIRSTBIT_APPOINTMENT_COMPONENT_DESC"),
    "PATH" => [
        "ID" => "firstbit_components",
        "NAME" => Loc::getMessage("FIRSTBIT_APPOINTMENT_VENDOR_NAME"),
        "CHILD" => [
            "ID" => "appointment",
            "NAME" => Loc::getMessage("FIRSTBIT_APPOINTMENT_CATEGORY_NAME")
        ]
    ],
    "CACHE_PATH" => "Y",
];