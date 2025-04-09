<?php
/**
 * @global CUser $USER
 * @global CMain $APPLICATION
 */

use ANZ\Appointment\Admin\Page\Option\ModuleSettingsPage;
use ANZ\Appointment\Config\Options;
use Bitrix\Main\Localization\Loc;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once ($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

try
{
    (new ModuleSettingsPage(new Options\Module()))->draw();
}
catch (Exception $e)
{
    ShowError(Loc::getMessage($e->getMessage()));
}

require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");