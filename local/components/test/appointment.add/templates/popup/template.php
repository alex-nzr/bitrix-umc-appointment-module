<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * @var string $templateFolder
 * @var array $arResult
 * @var array $arParams
 */

use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;

Loc::loadMessages(__FILE__);
try
{
    Extension::load(['firstbit.appointment.popup']);
}
catch (LoaderException $e)
{
    ShowError(Loc::getMessage('FIRSTBIT_APPOINTMENT_POPUP_EXTENSION_ERROR'));
}
?>
<div id="appointment-popup-root"></div>

<script>
    BX.ready(function(){
        if (BX.AppointmentPopup){
            BX.AppointmentPopup.init(<?=CUtil::PhpToJSObject($arResult['JS_SETTINGS'])?>);
        }
    })
</script>
