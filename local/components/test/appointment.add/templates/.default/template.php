<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
\Bitrix\Main\UI\Extension::load("ui.buttons");

use FirstBit\Appointment\Utils\Utils;

try {
    Utils::print(\FirstBit\Appointment\Model\RecordTable::query()
        ->setSelect(['USER.ID','USER.NAME','USER.LAST_NAME'])
        ->setFilter(['>USER_ID' => 0])
        ->exec()
        ->fetch());
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
<form id="testForm">
    <?=bitrix_sessid_post()?>
    <button data-action="add"    class="ui-btn ui-btn-success ui-btn-sm" type="button">Create Item</button>
    <button data-action="update" class="ui-btn ui-btn-success ui-btn-sm" type="button">Update Item</button>
    <button data-action="delete" class="ui-btn ui-btn-success ui-btn-sm" type="button">Delete Item</button>
</form>
