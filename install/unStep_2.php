<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.07.2022
 * ==================================================
*/
global $APPLICATION;

use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;

if (!check_bitrix_sessid()) {
    return;
}
$request = Context::getCurrent()->getRequest();
if ($ex = $APPLICATION->GetException())
{
    CAdminMessage::ShowMessage(array(
        "TYPE" => "ERROR",
        "MESSAGE" => Loc::getMessage("ANZ_APPOINTMENT_UNINSTALL_ERROR"),
        "DETAILS" => $ex->GetString(),
        "HTML" => true,
    ));
}
else
{
    CAdminMessage::ShowNote(Loc::getMessage("ANZ_APPOINTMENT_UNINSTALL_OK"));
}
?>
<form action="<?=$request->getRequestedPage();?>">
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
	<input type="submit" name="" value="<?=Loc::getMessage("MOD_BACK"); ?>">
<form>