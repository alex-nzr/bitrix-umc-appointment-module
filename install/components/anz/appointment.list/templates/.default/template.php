<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * @global CMain $APPLICATION
 * @var string $templateFolder
 * @var array $arResult
 * @var array $arParams
 */
?>
    <div class="adm-toolbar-panel-container">
        <div class="adm-toolbar-panel-flexible-space">
            <?php $APPLICATION->includeComponent(
                "bitrix:main.ui.filter",
                "",
                $arResult['FILTER_PARAMS'],
                false,
                ["HIDE_ICONS" => true]
            );?>
        </div>
    </div>
<?php
$APPLICATION->includeComponent(
    "bitrix:main.ui.grid",
    "",
    $arResult['GRID_PARAMS'],
    false,
    ["HIDE_ICONS" => "Y"]
);?>