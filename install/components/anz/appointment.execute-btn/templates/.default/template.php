<?php
/**
 * @var \ANZ\Appointment\Component\ExecuteBtnComponent $component
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
        <?=Loc::getMessage("ANZ_APPOINTMENT_EXCHANGE_START_BTN")?>
    </button>
    <span style="display: block;text-align: center;"></span>
</div>

<script>
    BX.ready(() => {
        const <?=uniqid('exchangeBtn')?> = new ExecuteBtn(
            `<?=$buttonId?>`,
            `<?=Constants::OPTION_KEY_EXCHANGE_NEXT_EXEC_DATE?>`,
            `<?=Loc::getMessage('ANZ_APPOINTMENT_EXCHANGE_MANUAL_DONE')?>`,
            `<?=$arParams['SITE_ID']?>`
        );
    })
</script>