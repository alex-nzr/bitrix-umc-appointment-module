<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * @global CMain $APPLICATION
 * @var string $templateFolder
 * @var array $arResult
 * @var array $arParams
 * @var \ANZ\Appointment\Component\Appointment\ItemsList\UmcComponent $component
 */
?>
    <div class="adm-toolbar-panel-container">
        <div class="adm-toolbar-panel-flexible-space">
            <?php $APPLICATION->includeComponent(
                "bitrix:main.ui.filter",
                "",
                $arResult['FILTER_PARAMS'],
                null,
                ["HIDE_ICONS" => "Y"]
            );?>
        </div>
    </div>
<?php
$APPLICATION->includeComponent(
    "bitrix:main.ui.grid",
    "",
    $arResult['GRID_PARAMS'],
    null,
    ["HIDE_ICONS" => "Y"]
);?>
<script>
    BX.ready(function() {
        const namespace = BX.namespace('Anz.Appointment.Admin');
        namespace.AppointmentList = new AppointmentList();
    })
</script>
