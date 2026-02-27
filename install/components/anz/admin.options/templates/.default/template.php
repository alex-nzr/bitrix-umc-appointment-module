<?php
/**
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @var string $templateFolder
 * @var array $arParams
 * @var array $arResult
 * @var \ANZ\Appointment\Component\Admin\AdminOptionsComponent $component
 * @var \CBitrixComponentTemplate $this
 */

use ANZ\Appointment\Config\Constants;
use Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$error = $APPLICATION->GetException() ? $APPLICATION->GetException()->GetString() : null;
if (!empty($error))
{
    ?>
    <div class="ui-alert ui-alert-danger">
        <span class="ui-alert-message"><?=$error?></span>
    </div>
    <?php
}

$formId = uniqid('admin_form_');
$component->getTabControl()->Begin();
try
{
    ?>
    <div class="ui-alert ui-alert-success">
        <span class="ui-alert-message"><?=Loc::getMessage('ANZ_APPOINTMENT_MODULE_SETTINGS_INFO')?></span>
    </div>

        <form method="POST" id="<?=$formId?>" action="<?=$arResult['FORM_ACTION']?>" name="<?=$arResult['MODULE_ID']?>_settings" enctype="multipart/form-data">
            <?php
            foreach ($arResult['TABS'] as $arTab)
            {
                if(is_array($arTab['OPTIONS']))
                {
                    $component->getTabControl()->BeginNextTab();
                    foreach($arTab['OPTIONS'] as $option)
                    {
                        $component->drawSettingsRow($arResult['MODULE_ID'], $option);
                    }
                }
            }

            $component->getTabControl()->Buttons();?>

            <?=bitrix_sessid_post();?>
            <input type="submit" name="Update" value="<?=Loc::getMessage('MAIN_SAVE')?>" class="adm-btn-save">
            <input type="reset"  name="reset" value="<?=Loc::getMessage('MAIN_RESET')?>">
        </form>
        <script>
            BX.ready(function() {
                const namespace = BX.namespace('Anz.Appointment.Admin');
                namespace.ModuleOptions = new ModuleOptions(<?=json_encode([
                    'formId' => $formId,
                    'jsExtOptionKey' => Constants::OPTION_KEY_JS_EXTENSION,
                    'useCustomBtnOptionKey' => Constants::OPTION_KEY_USE_CUSTOM_BTN,
                    'customBtnSelectorOptionKey' => Constants::OPTION_KEY_CUSTOM_BTN_SELECTOR,
                    'mainBtnBgOptionKey' => Constants::OPTION_KEY_MAIN_BTN_BG,
                    'mainBtnTextColorOptionKey' => Constants::OPTION_KEY_MAIN_BTN_TEXT_CLR,
                ])?>);
            })
        </script>
    <?php
    $component->getTabControl()->End();
}
catch (Exception $e)
{
    $component->showMessage($e->getMessage(), true);
}