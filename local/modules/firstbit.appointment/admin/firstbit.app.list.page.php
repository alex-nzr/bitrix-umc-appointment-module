<?php
/**
 * Bitrix vars
 * @global CUser $USER
 * @global CMain $APPLICATION
 */
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('FIRSTBIT_ADMIN_LIST_PAGE_TITLE'));

require_once ($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
?>

    <p>TODO - grid with records list and actions to management</p>
<p>TODO - add styles and icons to menu</p>

<?require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");?>