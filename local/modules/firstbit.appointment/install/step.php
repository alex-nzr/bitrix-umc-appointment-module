<?php
use Bitrix\Main\Localization\Loc;
global $APPLICATION;

if (!check_bitrix_sessid()) {
    $APPLICATION->ThrowException("Wrong session id");
}

if ($ex = $APPLICATION->GetException())
{
    CAdminMessage::ShowMessage(array(
        "TYPE" => "ERROR",
        "MESSAGE" => Loc::getMessage("FIRSTBIT_APPOINTMENT_INSTALL_ERROR"),
        "DETAILS" => $ex->GetString(),
        "HTML" => true,
    ));
}
else
{
    CAdminMessage::ShowNote(Loc::getMessage("FIRSTBIT_APPOINTMENT_INSTALL_OK"));
}
?>
<form action="<?=$APPLICATION->GetCurPage();?>">
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID ?>">
	<input type="submit" name="submit" value="<?=Loc::getMessage("FIRSTBIT_APPOINTMENT_INSTALL_BACK");?>">
<form>