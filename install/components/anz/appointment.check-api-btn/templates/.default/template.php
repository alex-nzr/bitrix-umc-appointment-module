<?php
/**
 * @var \ANZ\Appointment\Component\CheckApiBtnComponent $component
 * @var array $arParams
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use ANZ\Appointment\Config\Constants;
use Bitrix\Main\Localization\Loc;

$buttonId = str_replace(['.', ':'], '_', uniqid($component->getName()));
?>
<div>
    <button type="button" id="<?=$buttonId?>" class="ui-btn ui-btn-main"
            style="display:block;margin: 5px auto;border-radius: 5px;">
        <?=Loc::getMessage("ANZ_APPOINTMENT_API_CHECK_BTN")?>
    </button>
    <span style="display: block;text-align: center;">
        <?=Loc::getMessage('ANZ_APPOINTMENT_API_CHECK_NOT_APPLIED')?>
    </span>
</div>
<script>
    BX.ready(() => {
        const <?=uniqid('checkApiBtn')?> = new CheckApiBtn(
            `<?=$buttonId?>`,
            `<?=Constants::OPTION_KEY_EXCHANGE_MODE?>`,
            `<?=Constants::OPTION_KEY_API_WS_URL?>`,
            `<?=Constants::OPTION_KEY_API_WS_LOGIN?>`,
            `<?=Constants::OPTION_KEY_API_WS_PASSWORD?>`,
            `<?=Constants::OPTION_KEY_API_HS_TOKEN?>`,
            `<?=Loc::getMessage('ANZ_APPOINTMENT_API_CHECK_SUCCESS')?>`,
            `<?=Loc::getMessage('ANZ_APPOINTMENT_API_CHECK_ERROR')?>`
        );
    })
</script>