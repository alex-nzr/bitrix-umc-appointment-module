<?php
/**
 * Bitrix vars
 * @global CUser $USER
 * @global CMain $APPLICATION
 */
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use FirstBit\Appointment\Model\RecordTable;
use FirstBit\Appointment\Services\Admin\ListPageManager;

$APPLICATION->SetTitle(Loc::getMessage('FIRSTBIT_ADMIN_LIST_PAGE_TITLE'));
$APPLICATION->SetAdditionalCSS('/bitrix/css/main/grid/webform-button.css');

Loc::loadMessages(__FILE__);
$moduleID = 'firstbit.appointment';

try {
    if (!Loader::includeModule($moduleID)){
        throw new Exception(Loc::getMessage('FIRSTBIT_APPOINTMENT_MODULE_NOT_LOADED'));
    }

    if ($APPLICATION->GetGroupRight($moduleID) < 'W'){
        throw new Exception(Loc::getMessage('FIRSTBIT_APPOINTMENT_ACCESS_DENIED'));
    }

    $gridId = 'firstbit_appointment_admin_grid';
    $listPageManager = new ListPageManager(RecordTable::class);
    $filterParams = [
        'FILTER_ID' => $gridId,
    ];
    $gridParameters = [
        'GRID_ID' => $gridId,
        'COLUMNS' => $listPageManager->getColumns(),
        'ROWS'    => $listPageManager->getRows(),
    ];

    //\FirstBit\Appointment\Utils\Utils::print($gridParameters);

    require_once ($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

    ?>
    <div class="adm-toolbar-panel-container">
        <div class="adm-toolbar-panel-flexible-space">
            <?php $APPLICATION->includeComponent(
                "bitrix:main.ui.filter",
                "",
                $filterParams,
                false,
                array("HIDE_ICONS" => true)
            );?>
        </div>
    </div>
    <?php
    $APPLICATION->includeComponent(
        "bitrix:main.ui.grid",
        "",
        $gridParameters,
        false, array("HIDE_ICONS" => "Y")
    );
}
catch (Exception $e){
    ShowError(Loc::getMessage($e->getMessage()));
}

require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");?>