<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - firstbit.app.list.page.php
 * 10.07.2022 22:37
 * ==================================================
 */

/**
 * Bitrix vars
 * @global CUser $USER
 * @global CMain $APPLICATION
 */
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use FirstBit\Appointment\Services\Admin\ListPageManager;
use FirstBit\Appointment\Services\Container;

$APPLICATION->SetTitle(Loc::getMessage('FIRSTBIT_ADMIN_LIST_PAGE_TITLE'));

Loc::loadMessages(__FILE__);
$moduleID = 'firstbit.appointment';

try {
    if (!Loader::includeModule($moduleID)){
        throw new Exception(Loc::getMessage('FIRSTBIT_APPOINTMENT_MODULE_NOT_LOADED'));
    }

    if ($APPLICATION->GetGroupRight($moduleID) < 'W'){
        throw new Exception(Loc::getMessage('FIRSTBIT_APPOINTMENT_ACCESS_DENIED'));
    }

    Extension::load(['ui.buttons', $moduleID.'.admin']);

    $recordDataClass = Container::getInstance()->getRecordDataClass();

    $gridId = 'firstbit_appointment_admin_grid';
    $listPageManager = new ListPageManager($recordDataClass, $gridId);
    $navObject = $listPageManager->getPageNavigation();
    $columns = $listPageManager->getColumns();
    $rows = $listPageManager->getRows();
    $totalCount = $navObject->getRecordCount();

    $filterParams = [
        'FILTER_ID' => $gridId,
        "GRID_ID"   => $gridId,
        'FILTER'    => $listPageManager->getFilterSettings(),
        'ENABLE_LIVE_SEARCH' => true,
        'ENABLE_LABEL' => true
    ];
    $gridParameters = [
        'GRID_ID'       => $gridId,
        'NAV_OBJECT'    => $navObject,
        'COLUMNS'       => $columns,
        'ROWS'          => $rows,
        'AJAX_MODE'     => 'Y',
        'AJAX_ID'       => CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
        'PAGE_SIZES'    => [
            ['NAME' => "5",   'VALUE' => '5'],
            ['NAME' => '10',  'VALUE' => '10'],
            ['NAME' => '20',  'VALUE' => '20'],
            ['NAME' => '50',  'VALUE' => '50'],
            ['NAME' => '100', 'VALUE' => '100']
        ],
        'TOTAL_ROWS_COUNT'          => $totalCount,
        'SHOW_CHECK_ALL_CHECKBOXES' => true,
        'SHOW_ROW_ACTIONS_MENU'     => true,
        'SHOW_ROW_CHECKBOXES'       => true,
        'SHOW_GRID_SETTINGS_MENU'   => true,
        'SHOW_NAVIGATION_PANEL'     => true,
        'SHOW_PAGINATION'           => true,
        'SHOW_SELECTED_COUNTER'     => true,
        'SHOW_TOTAL_COUNTER'        => true,
        'SHOW_PAGESIZE'             => true,
        'SHOW_ACTION_PANEL'         => true,
        'ACTION_PANEL'              => $listPageManager->getGroupActionPanel(),
        'ALLOW_COLUMNS_SORT'        => true,
        'ALLOW_COLUMNS_RESIZE'      => true,
        'ALLOW_HORIZONTAL_SCROLL'   => true,
        'ALLOW_SORT'                => true,
        'ALLOW_PIN_HEADER'          => true,
        'AJAX_OPTION_HISTORY'       => 'N',
        'AJAX_OPTION_JUMP'          => 'N',
    ];

    require_once ($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

    ?>
    <div class="adm-toolbar-panel-container">
        <div class="adm-toolbar-panel-flexible-space">
            <?php $APPLICATION->includeComponent(
                "bitrix:main.ui.filter",
                "",
                $filterParams,
                false,
                ["HIDE_ICONS" => true]
            );?>
        </div>
    </div>
    <?php
    $APPLICATION->includeComponent(
        "bitrix:main.ui.grid",
        "",
        $gridParameters,
        false,
        ["HIDE_ICONS" => "Y"]
    );
}
catch (Exception $e){
    ShowError(Loc::getMessage($e->getMessage()));
}

require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");?>