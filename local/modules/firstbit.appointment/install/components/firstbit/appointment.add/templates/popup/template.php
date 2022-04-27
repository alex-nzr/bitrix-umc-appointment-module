<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * @var string $templateFolder
 * @var array $arResult
 * @var array $arParams
 */

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

?>
<div id="appointment-popup-root"></div>

<script>
    BX.ready(function(){
        if (BX.FirstBit?.Appointment?.Popup){
            BX.FirstBit.Appointment.Popup.init(<?=CUtil::PhpToJSObject($arResult['JS_SETTINGS'])?>);
        }
    })
</script>
