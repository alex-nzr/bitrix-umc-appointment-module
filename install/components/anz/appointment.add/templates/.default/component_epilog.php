<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use ANZ\Appointment\Config\Configuration;
use Bitrix\Main\UI\Extension;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
try
{
    Extension::load([Configuration::getInstance()->getJsExtensionName()]);
}
catch (Exception $e)
{
    ShowError(Loc::getMessage('ANZ_APPOINTMENT_POPUP_EXTENSION_ERROR'));
}