<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * @var string $templateFolder
 * @var array $arResult
 * @var array $arParams
 */
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$wrapperId          = "appointment-widget-wrapper";
$widgetBtnWrapId    = "appointment-button-wrapper";
$widgetBtnId        = "appointment-button";
$formId             = 'appointment-form';
$messageNodeId      = 'appointment-form-message';
$submitBtnId        = "appointment-form-button";
$appResultBlockId   = "appointment-result-block";
?>
<script>
    const selectionNodes = {};
    const textNodes      = {};
    const defaultText    = {};
</script>

<div class="widget-wrapper" id="<?=$wrapperId?>">
    <div class="appointment-button-wrapper loading" id="<?=$widgetBtnWrapId?>">
        <button id="<?=$widgetBtnId?>"></button>
        <div class="appointment-loader">
            <?php for ($i = 1; $i <= 5; $i++ ):?>
                <div class="wBall" id="wBall_<?=$i?>"><div class="wInnerBall"></div></div>
            <?php endfor;?>
        </div>
    </div>

    <form id="<?=$formId?>" class="appointment-form">
        <?php foreach($arResult["SELECTION_BLOCKS"] as $key => $text):?>
            <div class="selection-block <?=($key !== $arResult["CLINICS_KEY"] ? 'hidden' : '')?>" id="<?=$key?>_block">
                <p class="selection-item-selected" id="<?=$key?>_selected"><?=$text?></p>
                <ul class="appointment-form_head_list selection-item-list" id="<?=$key?>_list"></ul>
                <input type="hidden" name="<?=$key?>" id="<?=$key?>_value">
            </div>
            <script>
                selectionNodes['<?=$key?>'] = {
                    "blockId": "<?=$key?>_block",
                    "listId": "<?=$key?>_list",
                    "selectedId": "<?=$key?>_selected",
                    "inputId": "<?=$key?>_value",
                    "isRequired": <?=($key === $arResult["SERVICES_KEY"] ? 'false' : 'true')?>
                }
                defaultText['<?=$key?>'] = '<?=$text?>';
            </script>
        <?php endforeach;?>

        <?php foreach($arResult["TEXT_BLOCKS"] as $blockAttrs):?>
            <label class="appointment-form_input-wrapper">
                <?php if(!empty($blockAttrs["type"])):?>
                    <input <?php foreach($blockAttrs as $attrName => $attrValue){ echo $attrName.'='.'"'.$attrValue.'"'; }?>/>
                <?php else:?>
                    <textarea <?php foreach($blockAttrs as $attrName => $attrValue){ echo $attrName.'='.'"'.$attrValue.'"'; }?>></textarea>
                <?php endif;?>
            </label>
            <script>
                textNodes["<?=$blockAttrs["name"]?>"] = {
                    "inputId": "<?=$blockAttrs["id"]?>",
                    "isRequired": <?=($blockAttrs["name"] === "comment" ? 'false' : 'true')?>
                };
            </script>
        <?php endforeach;?>

        <p id="<?=$messageNodeId?>"></p>

        <div class="appointment-form_submit-wrapper">
            <button type="submit" id="<?=$submitBtnId?>" class="appointment-form_button">Записаться на приём</button>
        </div>

        <p class="appointment-info-message">
            Отправляя данные, вы соглашаетесь с <a href="<?=$arResult["PRIVACY_PAGE_URL"]?>">политикой конфиденциальности</a> сайта
        </p>

        <div id="<?=$appResultBlockId?>"><p></p></div>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', ()=>{
        window.appointmentWidget.init({
            "useServices": '<?=$arResult["USE_NOMENCLATURE"]?>',
            "selectDoctorBeforeService": '<?=$arResult["SELECT_DOCTOR_BEFORE_SERVICE"]?>',
            "useTimeSteps": '<?=$arResult["USE_TIME_STEPS"]?>',
            "timeStepDurationMinutes": '<?=$arResult["TIME_STEP_DURATION"]?>',
            "strictCheckingOfRelations": '<?=$arResult["STRICT_CHECKING_RELATIONS"]?>',
            "showDoctorsWithoutDepartment": '<?=$arResult["SHOW_DOCTORS_WITHOUT_DEPARTMENT"]?>',
            "widgetBtnWrapId": '<?=$widgetBtnWrapId?>',
            "wrapperId": "<?=$wrapperId?>",
            "formId": '<?=$formId?>',
            "widgetBtnId": '<?=$widgetBtnId?>',
            "messageNodeId": '<?=$messageNodeId?>',
            "submitBtnId": '<?=$submitBtnId?>',
            "appResultBlockId": '<?=$appResultBlockId?>',
            "dataKeys": {
                "clinicsKey": '<?=$arResult["CLINICS_KEY"]?>',
                "specialtiesKey": '<?=$arResult["SPECIALTIES_KEY"]?>',
                "servicesKey": '<?=$arResult["SERVICES_KEY"]?>',
                "employeesKey": '<?=$arResult["EMPLOYEES_KEY"]?>',
                "scheduleKey": '<?=$arResult["SCHEDULE_KEY"]?>',
            },
            "selectionNodes": selectionNodes,
            "textNodes": textNodes,
            "defaultText": defaultText,
            "isUpdate": false,
        });
    })
</script>
