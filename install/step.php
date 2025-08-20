<?php

use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
global $APPLICATION;

if (!check_bitrix_sessid()) {
    $APPLICATION->ThrowException("Wrong session id");
}
$request = Context::getCurrent()->getRequest();
if ($ex = $APPLICATION->GetException())
{
    CAdminMessage::ShowMessage(array(
        "TYPE" => "ERROR",
        "MESSAGE" => Loc::getMessage("ANZ_APPOINTMENT_INSTALL_ERROR"),
        "DETAILS" => $ex->GetString(),
        "HTML" => true,
    ));
}
else
{
    CAdminMessage::ShowNote(Loc::getMessage("ANZ_APPOINTMENT_INSTALL_OK"));
}
?>
<form action="<?=$request->getRequestedPage();?>">
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID ?>">
	<input type="submit" name="submit" value="<?=Loc::getMessage("ANZ_APPOINTMENT_INSTALL_BACK");?>">
<form>