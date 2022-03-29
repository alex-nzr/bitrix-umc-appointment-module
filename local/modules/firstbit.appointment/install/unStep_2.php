<?php
global $APPLICATION;
use Bitrix\Main\Localization\Loc;

if (!check_bitrix_sessid()) {
    return;
}

if ($ex = $APPLICATION->GetException())
{
    CAdminMessage::ShowMessage(array(
        "TYPE" => "ERROR",
        "MESSAGE" => Loc::getMessage("FIRSTBIT_APPOINTMENT_UNINSTALL_ERROR"),
        "DETAILS" => $ex->GetString(),
        "HTML" => true,
    ));
}
else
{
    CAdminMessage::ShowNote(Loc::getMessage("FIRSTBIT_APPOINTMENT_UNINSTALL_OK"));
}
?>
<form action="<?=$APPLICATION->GetCurPage();?>">
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
	<input type="submit" name="" value="<?=Loc::getMessage("MOD_BACK"); ?>">
<form>