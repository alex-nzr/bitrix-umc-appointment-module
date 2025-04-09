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

use ANZ\Appointment\Internals\ServiceManager;
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$component->getTabControl()->Begin();
try
{
    ?>
        <form method="POST" action="<?=$arResult['FORM_ACTION']?>" name="<?=$arResult['MODULE_ID']?>_settings" enctype="multipart/form-data">
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
    <?php
    $component->getTabControl()->End();
}
catch (Exception $e)
{
    $component->showMessage($e->getMessage(), true);
}