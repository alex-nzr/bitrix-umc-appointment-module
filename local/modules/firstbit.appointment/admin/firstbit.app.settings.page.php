<?php
/**
 * Bitrix vars
 * @global CUser $USER
 * @global CMain $APPLICATION
 */

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$APPLICATION->SetTitle(Loc::getMessage('FIRSTBIT_ADMIN_SETTINGS_PAGE_TITLE'));

$moduleID = 'firstbit.appointment';

require_once ($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

try {
    if (!Loader::includeModule($moduleID)){
        throw new Exception(Loc::getMessage('FIRSTBIT_APPOINTMENT_MODULE_NOT_LOADED'));
    }

    if ($APPLICATION->GetGroupRight($moduleID) < 'W'){
        throw new Exception(Loc::getMessage('FIRSTBIT_APPOINTMENT_ACCESS_DENIED'));
    }

    require_once (__DIR__."/../options.php");
}
catch (Exception $e){
    ShowError(Loc::getMessage($e->getMessage()));
}

require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");