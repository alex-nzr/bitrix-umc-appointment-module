<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - template.php
 * 10.07.2022 22:37
 * ==================================================
 */

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