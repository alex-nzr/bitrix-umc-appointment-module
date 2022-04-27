<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\LoaderException;
use Bitrix\Main\UI\Extension;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
try
{
    Extension::load(['firstbit.appointment.popup']);
}
catch (LoaderException $e)
{
    ShowError(Loc::getMessage('FIRSTBIT_APPOINTMENT_POPUP_EXTENSION_ERROR'));
}