<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - component_epilog.php
 * 10.07.2022 22:37
 * ==================================================
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\LoaderException;
use Bitrix\Main\UI\Extension;
use Bitrix\Main\Localization\Loc;
use ANZ\Appointment\Config\Constants;

Loc::loadMessages(__FILE__);
try
{
    Extension::load([Constants::APPOINTMENT_JS_EXTENSION]);
}
catch (LoaderException $e)
{
    ShowError(Loc::getMessage('ANZ_APPOINTMENT_POPUP_EXTENSION_ERROR'));
}