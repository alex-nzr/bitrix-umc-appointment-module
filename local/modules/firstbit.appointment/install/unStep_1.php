<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - unStep_1.php
 * 10.07.2022 22:37
 * ==================================================
 */
global $APPLICATION;

use ANZ\Appointment\Config\Constants;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (!check_bitrix_sessid()) {
    return;
}

?>
<form action="<?=$APPLICATION->GetCurPage();?>">
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
    <input type="hidden" name="id" value="<?= Constants::APPOINTMENT_MODULE_ID?>">
    <input type="hidden" name="uninstall" value="Y">
    <input type="hidden" name="step" value="2">
    <?php CAdminMessage::ShowMessage(Loc::getMessage("ANZ_APPOINTMENT_UNINSTALL_WARN"))?>
    <p><?=Loc::getMessage("ANZ_APPOINTMENT_UNINSTALL_SAVE")?></p>
    <label for="saveData" style="display: block; margin-bottom: 20px">
        <input type="checkbox" name="saveData" id="saveData" value="Y" checked>
        <?=Loc::getMessage("ANZ_APPOINTMENT_UNINSTALL_SAVE_TABLES")?>
    </label>
    <?=bitrix_sessid_post()?>
	<input type="submit" name="ok" value="<?=Loc::getMessage("ANZ_APPOINTMENT_UNINSTALL_ACCEPT"); ?>">
<form>