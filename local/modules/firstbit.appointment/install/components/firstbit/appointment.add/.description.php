<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - description.php
 * 10.07.2022 22:37
 * ==================================================
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

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