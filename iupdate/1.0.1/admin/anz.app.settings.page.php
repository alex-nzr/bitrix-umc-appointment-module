<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - anz.app.settings.page.php
 * 10.07.2022 22:37
 * ==================================================
 */

/**
 * Bitrix vars
 * @global CUser $USER
 * @global CMain $APPLICATION
 */

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$APPLICATION->SetTitle(Loc::getMessage('ANZ_ADMIN_SETTINGS_PAGE_TITLE'));

$moduleID = 'anz.appointment';

require_once ($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

try {
    if (!Loader::includeModule($moduleID)){
        throw new Exception(Loc::getMessage('ANZ_APPOINTMENT_MODULE_NOT_LOADED'));
    }

    if ($APPLICATION->GetGroupRight($moduleID) < 'W'){
        throw new Exception(Loc::getMessage('ANZ_APPOINTMENT_ACCESS_DENIED'));
    }

    require_once (__DIR__."/../options.php");
}
catch (Exception $e){
    ShowError(Loc::getMessage($e->getMessage()));
}

require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");