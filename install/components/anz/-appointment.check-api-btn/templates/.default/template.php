<?php
/**
 * @var \Chelbit\Umc\Component\Admin\CheckApiBtnComponent $component
 * @var array $arParams
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;
use Chelbit\Umc\Config\Model\SettingsTable;

$buttonId = str_replace(['.', ':'], '_', uniqid($component->getName()));
?>
<div>
    <button type="button" id="<?=$buttonId?>" class="ui-btn ui-btn-main"
            style="display:block;margin: 5px auto;border-radius: 5px;">
        <?=Loc::getMessage("CHELBIT_UMC_MIS_API_CHECK_BTN")?>
    </button>
    <span style="display: block;text-align: center;">
        <?=Loc::getMessage('CHELBIT_UMC_MIS_API_CHECK_NOT_APPLIED')?>
    </span>
</div>
<script>
    BX.ready(() => {
        const <?=uniqid('checkApiBtn')?> = new CheckApiBtn(
            `<?=$buttonId?>`,
            `<?=SettingsTable::FIELD_NAME_MIS_URL?>`,
            `<?=SettingsTable::FIELD_NAME_MIS_LOGIN?>`,
            `<?=SettingsTable::FIELD_NAME_MIS_PASSWORD?>`,
            `<?=SettingsTable::FIELD_NAME_MIS_TOKEN?>`,
            `<?=Loc::getMessage('CHELBIT_UMC_MIS_API_CHECK_SUCCESS')?>`,
            `<?=Loc::getMessage('CHELBIT_UMC_MIS_API_CHECK_ERROR')?>`,
            `<?=$arParams['SITE_ID']?>`
        );
    })
</script>