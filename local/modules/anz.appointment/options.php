<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - options.php
 * 10.07.2022 22:37
 * ==================================================
 */

/** @var \CMain $APPLICATION */

use ANZ\Appointment\Config\Constants;
use ANZ\Appointment\Config\OptionManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;


Loc::loadMessages(__FILE__);


$module_id = 'anz.appointment';

try
{
    if ($APPLICATION->GetGroupRight($module_id) < "W")
    {
        $APPLICATION->AuthForm(Loc::getMessage("ANZ_APPOINTMENT_ACCESS_DENIED"));
    }

    Extension::load([$module_id.'.admin']);

    if(!Loader::includeModule($module_id)){
        throw new Exception(Loc::getMessage("ANZ_APPOINTMENT_MODULE_NOT_LOADED"));
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