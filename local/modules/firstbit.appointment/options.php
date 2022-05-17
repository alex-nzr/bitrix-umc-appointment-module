<?php
/** @var \CMain $APPLICATION */
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use FirstBit\Appointment\Config\OptionManager;

Loc::loadMessages(__FILE__);


$module_id = 'firstbit.appointment';

try
{
    if ($APPLICATION->GetGroupRight($module_id) < "W")
    {
        $APPLICATION->AuthForm(Loc::getMessage("FIRSTBIT_APPOINTMENT_ACCESS_DENIED"));
    }

    Extension::load([$module_id.'.admin']);

    if(!Loader::includeModule($module_id)){
        throw new Exception(Loc::getMessage("FIRSTBIT_APPOINTMENT_MODULE_NOT_LOADED"));
    }
	$optionManager = new OptionManager($module_id);
    $optionManager->processRequest();
    $optionManager->startDrawHtml();

    //show access tab. It works only in 'options.php' context, therefore, html rendering split into two parts
    $optionManager->tabControl->BeginNextTab();
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");

    $optionManager->endDrawHtml();
}
catch(Exception $e)
{
	ShowError($e->getMessage());
}