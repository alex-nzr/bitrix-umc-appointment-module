<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - unStep_2.php
 * 10.07.2022 22:37
 * ==================================================
 */
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