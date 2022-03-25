<?php
global $APPLICATION;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
if (!check_bitrix_sessid()) {
    return;
}

?>
<form action="<?=$APPLICATION->GetCurPage();?>">
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
    <input type="hidden" name="id" value="firstbit.appointment">
    <input type="hidden" name="uninstall" value="Y">
    <input type="hidden" name="step" value="2">
    <?php CAdminMessage::ShowMessage(Loc::getMessage("MOD_UNINST_WARN"))?>
    <p><?=Loc::getMessage("MOD_UNINST_SAVE")?></p>
    <label for="saveData" style="display: block; margin-bottom: 20px">
        <input type="checkbox" name="saveData" id="saveData" value="Y" checked>
        <?=Loc::getMessage("MOD_UNINST_SAVE_TABLES")?>
    </label>
    <?=bitrix_sessid_post()?>
	<input type="submit" name="ok" value="<?=Loc::getMessage("MOD_UNINST_DEL"); ?>">
<form>