<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use FirstBit\Appointment\Config\OptionManager;

Loc::loadMessages(__FILE__);

$module_id = 'firstbit.appointment';

try
{
    if(!Loader::includeModule($module_id)){
        throw new Exception(Loc::getMessage("FIRSTBIT_APPOINTMENT_MODULE_NOT_LOADED"));
    }
	$optionManager = new OptionManager($module_id);
    $optionManager->processRequest();
    $optionManager->showHtml();
}
catch(Exception $e)
{
	ShowError($e->getMessage());
}