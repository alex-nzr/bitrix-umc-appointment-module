<?php
namespace FirstBit\Appointment\Config;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Request;
use CAdminTabControl;
use CControllerClient;
use Exception;
use function htmlSpecialCharsBx;
use function ShowError;

Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . "/modules/main/options.php");
Loc::loadMessages(__FILE__);

class OptionManager{

    private Request $request;
    private string  $moduleId;
    private array   $tabs;
    private string $formAction;
    public CAdminTabControl $tabControl;

    public function __construct(string $moduleId)
    {
        $this->request  = Context::getCurrent()->getRequest();
        $this->moduleId = $moduleId;
        $this->setTabs();
        $this->tabControl = new CAdminTabControl('tabControl', $this->tabs);
        $this->formAction = $this->request->getRequestedPage() . "?" . http_build_query([
            'mid'  => htmlSpecialCharsBx($this->request->get('mid')),
            'lang' => $this->request->get('lang')
        ]);
    }

    /**
     * @return void
     */
    protected function setTabs()
    {
        $this->tabs = [
            [
                'DIV'   => "settings_tab",
                'TAB'   => Loc::getMessage("FIRSTBIT_APPOINTMENT_MODULE_SETTINGS"),
                'ICON'  => '',
                'TITLE' => Loc::getMessage("FIRSTBIT_APPOINTMENT_MODULE_SETTINGS"),
                "OPTIONS" => [
                    Loc::getMessage("FIRSTBIT_APPOINTMENT_API_SETTINGS"),
                    [
                        'appointment_api_ws_url',
                        Loc::getMessage("FIRSTBIT_APPOINTMENT_API_ADDRESS"),
                        "http://localhost:3500/umc_corp/ws/ws1.1cws?wsdl",
                        ['text', 50]
                    ],
                    [
                        'appointment_api_db_login',
                        Loc::getMessage("FIRSTBIT_APPOINTMENT_API_LOGIN"),
                        "siteIntegration",
                        ['text', 50]
                    ],
                    [
                        'appointment_api_db_password',
                        Loc::getMessage("FIRSTBIT_APPOINTMENT_API_PASSWORD"),
                        "123456",
                        ['text', 50]
                    ],

                    Loc::getMessage("FIRSTBIT_APPOINTMENT_OTHER_SETTINGS"),
                    [
                        'appointment_api_schedule_days',
                        Loc::getMessage('FIRSTBIT_APPOINTMENT_SCHEDULE_PERIOD'),
                        Constants::DEFAULT_SCHEDULE_PERIOD_DAYS,
                        ['text', 5]
                    ],
                    [
                        'appointment_settings_default_duration',
                        Loc::getMessage('FIRSTBIT_APPOINTMENT_DEFAULT_DURATION'),
                        Constants::DEFAULT_APPOINTMENT_DURATION_SEC,
                        ['text', 5]
                    ],
                    [
                        'appointment_settings_use_nomenclature',
                        Loc::getMessage('FIRSTBIT_APPOINTMENT_USE_NOMENCLATURE'),
                        "Y",
                        ['checkbox', "Y"]
                    ],
                    [ 'note' => Loc::getMessage('FIRSTBIT_APPOINTMENT_USE_NOMENCLATURE_WARNING')],

                    [
                        'appointment_settings_select_doctor_before_service',
                        Loc::getMessage('FIRSTBIT_APPOINTMENT_SELECT_DOCTOR_BEFORE_SERVICE'),
                        "Y",
                        ['checkbox', "Y"]
                    ],
                    [ 'note' => Loc::getMessage('FIRSTBIT_APPOINTMENT_SELECT_DOCTOR_BEFORE_SERVICE_NOTE')],

                    [
                        'appointment_settings_use_time_steps',
                        Loc::getMessage('FIRSTBIT_APPOINTMENT_USE_TIME_STEPS'),
                        "N",
                        ['checkbox', "N"]
                    ],
                    [
                        'appointment_settings_time_step_duration',
                        Loc::getMessage('FIRSTBIT_APPOINTMENT_TIME_STEP_DURATION'),
                        "15",
                        ['text', 5]
                    ],
                    [ 'note' => Loc::getMessage('FIRSTBIT_APPOINTMENT_USE_TIME_STEPS_NOTE')],

                    [
                        'appointment_settings_strict_checking_relations',
                        Loc::getMessage('FIRSTBIT_APPOINTMENT_STRICT_CHECKING_RELATIONS'),
                        "Y",
                        ['checkbox', "Y"]
                    ],
                    [ 'note' => Loc::getMessage('FIRSTBIT_APPOINTMENT_STRICT_CHECKING_RELATIONS_NOTE')],

                    [
                        'appointment_settings_show_doctors_without_dpt',
                        Loc::getMessage('FIRSTBIT_APPOINTMENT_SHOW_DOCTORS_WITHOUT_DEPARTMENT'),
                        "Y",
                        ['checkbox', "Y"]
                    ],
                    [ 'note' => Loc::getMessage('FIRSTBIT_APPOINTMENT_SHOW_DOCTORS_WITHOUT_DEPARTMENT_NOTE')],

                    [
                        'appointment_settings_use_waiting_list',
                        Loc::getMessage('FIRSTBIT_APPOINTMENT_USE_WAITING_LIST'),
                        "N",
                        ['checkbox', "N"]
                    ],
                    [ 'note' => Loc::getMessage('FIRSTBIT_APPOINTMENT_USE_WAITING_LIST_NOTE')],

                    [
                        'appointment_settings_use_email_note',
                        Loc::getMessage('FIRSTBIT_APPOINTMENT_USE_EMAIL_NOTE'),
                        "Y",
                        ['checkbox', "Y"]
                    ],

                    [
                        'appointment_settings_privacy_page_url',
                        Loc::getMessage('FIRSTBIT_APPOINTMENT_PRIVACY_PAGE_URL'),
                        "javascript: void(0)",
                        ['text', 50]
                    ],
                ]
            ],
            [
                'DIV'   => "access_tab",
                'TAB'   => Loc::getMessage("FIRSTBIT_APPOINTMENT_TAB_RIGHTS"),
                'ICON'  => '',
                'TITLE' => Loc::getMessage("FIRSTBIT_APPOINTMENT_TAB_TITLE_RIGHTS"),
            ]
        ];
    }

    /**
     * @return void
     */
    public function processRequest(): void
    {
        try {
            if ($this->request->isPost() && $this->request->getPost('Update') && check_bitrix_sessid())
            {
                foreach ($this->tabs as $arTab)
                {
                    foreach ($arTab['OPTIONS'] as $arOption)
                    {
                        if(!is_array($arOption) || !empty($arOption['note']))
                        {
                            continue;
                        }
                        $optionName = $arOption[0];
                        $optionValue = $this->request->getPost($optionName);
                        Option::set(
                                $this->moduleId,
                                $optionName,
                                is_array($optionValue) ? implode(",", $optionValue) : $optionValue
                        );
                    }
                }
            }
        }
        catch (Exception $e){
            ShowError($e->getMessage());
        }
    }

    /**
     * @return void
     */
    public function startDrawHtml()
    {
        $this->tabControl->Begin();
        ?>
        <form method="POST" action="<?=$this->formAction?>" name="firstbit_appointment_settings">
        <?php
            foreach ($this->tabs as $arTab)
            {
                if(is_array($arTab['OPTIONS']))
                {
                    $this->tabControl->BeginNextTab();
                    $this->drawSettingsList($this->moduleId, $arTab['OPTIONS']);
                }

            }
    }

    /**
     * @return void
     */
    public function endDrawHtml()
    {
            $this->tabControl->Buttons();?>
            <?=bitrix_sessid_post();?>
            <input type="submit" name="Update" value="<?=Loc::getMessage('MAIN_SAVE')?>" class="adm-btn-save">
            <input type="reset"  name="reset" value="<?=Loc::getMessage('MAIN_RESET')?>">
        </form>
        <?php
        $this->tabControl->End();
    }

    /**
     * @param $module_id
     * @param $Option
     */
    protected function drawSettingsRow($module_id, $Option)
    {
        $arControllerOption = CControllerClient::GetInstalledOptions($module_id);
        if($Option === null)
        {
            return;
        }

        if(!is_array($Option)): ?>
            <tr class="heading">
                <td colspan="2"><?=$Option?></td>
            </tr>
        <? elseif(isset($Option["note"])): ?>
            <tr>
                <td colspan="2">
                    <div class="adm-info-message-wrap">
                        <div class="adm-info-message" style="display: block"><?=$Option["note"]?></div>
                    </div>
                </td>
            </tr>
        <? else:
            if ($Option[0] !== "")
            {
                $val = Option::get($module_id, $Option[0], $Option[2]);
            }
            else
            {
                $val = $Option[2];
            }
        ?>
            <tr>
                <?
                $this->renderLabel($Option);
                $this->renderInput($Option, $arControllerOption, $Option[0], $val);
                ?>
            </tr>
        <? endif;
    }

    protected function drawSettingsList($module_id, $arParams)
    {
        foreach($arParams as $Option)
        {
            $this->drawSettingsRow($module_id, $Option);
        }
    }

    protected function renderLabel($Option)
    {
        $type = $Option[3];
        $sup_text = array_key_exists(5, $Option) ? $Option[5] : '';
        $class = '';
        $label = '';
        switch ($type[0])
        {
            case "multiselectbox":
            case "textarea":
            case "statictext":
            case "statichtml":
                $class = 'adm-detail-valign-top';
                break;
            case "checkbox":
                $label = "<label for='". htmlSpecialCharsBx($Option[0])."'>".$Option[1]."</label>";
                break;
            default:
                $label = $Option[1];
        }

        ?>
        <td class="<?=$class?>" style="width: 50%">
            <?=$label?>
            <? if ($sup_text !== '') : ?>
                <span class="required"><sup><?=$sup_text?></sup></span>
            <?endif;?>
        </td>
        <?
    }

    protected function renderInput($Option, $arControllerOption, $fieldName, $val)
    {
        $type = $Option[3];
        $disabled = array_key_exists(4, $Option) && $Option[4] == 'Y' ? ' disabled' : '';
        ?>
        <td style="width: 50%">
            <label for="<?echo htmlSpecialCharsBx($Option[0])?>">
                <? if($type[0]=="checkbox"): ?>
                    <input type="checkbox" <?if(isset($arControllerOption[$Option[0]]))echo ' disabled title="'.GetMessage("MAIN_ADMIN_SET_CONTROLLER_ALT").'"';?>id="<?echo htmlSpecialCharsBx($Option[0])?>" name="<?=htmlSpecialCharsBx($fieldName)?>" value="Y"<?if($val=="Y")echo" checked";?><?=$disabled?><?if($type[2]<>'') echo " ".$type[2]?>>
                <? elseif($type[0]=="text" || $type[0]=="password"): ?>
                    <input type="<?echo $type[0]?>"<?if(isset($arControllerOption[$Option[0]]))echo ' disabled title="'.GetMessage("MAIN_ADMIN_SET_CONTROLLER_ALT").'"';?> size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlSpecialCharsBx($val)?>" name="<?=htmlSpecialCharsBx($fieldName)?>"<?=$disabled?><?=($type[0]=="password" || $type["noautocomplete"]? ' autocomplete="new-password"':'')?>><?
                elseif($type[0]=="selectbox"):
                    $arr = $type[1];
                    if(!is_array($arr))
                        $arr = array();
                ?>
                    <select name="<?=htmlSpecialCharsBx($fieldName)?>" <?if(isset($arControllerOption[$Option[0]]))echo ' disabled title="'.GetMessage("MAIN_ADMIN_SET_CONTROLLER_ALT").'"';?> <?=$disabled?>>
                        <? foreach($arr as $key => $v): ?>
                            <option value="<?echo $key?>"<?if($val==$key)echo" selected"?>><?echo htmlSpecialCharsBx($v)?></option>
                        <? endforeach; ?>
                    </select>
                <? elseif($type[0]=="multiselectbox"):
                    $arr = $type[1];
                    if(!is_array($arr))
                        $arr = array();
                    $arr_val = explode(",",$val);
                ?>
                    <select size="5" <?if(isset($arControllerOption[$Option[0]]))echo ' disabled title="'.GetMessage("MAIN_ADMIN_SET_CONTROLLER_ALT").'"';?> multiple name="<?=htmlSpecialCharsBx($fieldName)?>[]"<?=$disabled?>>
                        <? foreach($arr as $key => $v): ?>
                            <option value="<?echo $key?>"<?if(in_array($key, $arr_val)) echo " selected"?>><?echo htmlSpecialCharsBx($v)?></option>
                        <? endforeach; ?>
                    </select>
                <? elseif($type[0]=="textarea"): ?>
                    <textarea <?if(isset($arControllerOption[$Option[0]]))echo ' disabled title="'.GetMessage("MAIN_ADMIN_SET_CONTROLLER_ALT").'"';?> rows="<?echo $type[1]?>" cols="<?echo $type[2]?>" name="<?=htmlSpecialCharsBx($fieldName)?>"<?=$disabled?>><?echo htmlSpecialCharsBx($val)?></textarea>
                <? elseif($type[0]=="statictext"): ?>
                    <?=htmlSpecialCharsBx($val)?>
                <? elseif($type[0]=="statichtml"):?>
                    <?=$val?>
                <?endif;?>
            </label>
        </td><?
    }
}