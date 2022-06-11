<?php
namespace FirstBit\Appointment\Config;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Request;
use CAdminTabControl;
use CFile;
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

                    Loc::getMessage("FIRSTBIT_APPOINTMENT_USE_AUTO_INJECTING"),
                    [
                        'appointment_settings_use_auto_injecting',
                        Loc::getMessage('FIRSTBIT_APPOINTMENT_USE_AUTO_INJECTING_ON'),
                        "N",
                        ['checkbox']
                    ],
                    [ 'note' => Loc::getMessage('FIRSTBIT_APPOINTMENT_USE_AUTO_INJECTING_NOTE')],

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
                        "N",
                        ['checkbox']
                    ],
                    [ 'note' => Loc::getMessage('FIRSTBIT_APPOINTMENT_USE_NOMENCLATURE_WARNING')],

                    /*[
                        'appointment_settings_select_doctor_before_service',
                        Loc::getMessage('FIRSTBIT_APPOINTMENT_SELECT_DOCTOR_BEFORE_SERVICE'),
                        "N",
                        ['checkbox']
                    ],
                    [ 'note' => Loc::getMessage('FIRSTBIT_APPOINTMENT_SELECT_DOCTOR_BEFORE_SERVICE_NOTE')],*/

                    [
                        'appointment_settings_use_time_steps',
                        Loc::getMessage('FIRSTBIT_APPOINTMENT_USE_TIME_STEPS'),
                        "N",
                        ['checkbox']
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
                        "N",
                        ['checkbox']
                    ],
                    [ 'note' => Loc::getMessage('FIRSTBIT_APPOINTMENT_STRICT_CHECKING_RELATIONS_NOTE')],

                    [
                        'appointment_settings_show_doctors_without_dpt',
                        Loc::getMessage('FIRSTBIT_APPOINTMENT_SHOW_DOCTORS_WITHOUT_DEPARTMENT'),
                        "N",
                        ['checkbox']
                    ],
                    [ 'note' => Loc::getMessage('FIRSTBIT_APPOINTMENT_SHOW_DOCTORS_WITHOUT_DEPARTMENT_NOTE')],

                    [
                        'appointment_settings_use_waiting_list',
                        Loc::getMessage('FIRSTBIT_APPOINTMENT_USE_WAITING_LIST'),
                        "N",
                        ['checkbox']
                    ],
                    [ 'note' => Loc::getMessage('FIRSTBIT_APPOINTMENT_USE_WAITING_LIST_NOTE')],

                    [
                        'appointment_settings_use_email_note',
                        Loc::getMessage('FIRSTBIT_APPOINTMENT_USE_EMAIL_NOTE'),
                        "N",
                        ['checkbox']
                    ],

                    [
                        'appointment_settings_privacy_page_url',
                        Loc::getMessage('FIRSTBIT_APPOINTMENT_PRIVACY_PAGE_URL'),
                        "#",
                        ['text', 50]
                    ],

                    [
                        'appointment_settings_confirm_with',
                        Loc::getMessage('FIRSTBIT_APPOINTMENT_CONFIRM_WITH'),
                        Constants::CONFIRM_TYPE_NONE,
                        [
                            'select',
                            [
                                Constants::CONFIRM_TYPE_NONE  => Loc::getMessage('FIRSTBIT_APPOINTMENT_CONFIRM_WITH_NONE'),
                                Constants::CONFIRM_TYPE_PHONE => Loc::getMessage('FIRSTBIT_APPOINTMENT_CONFIRM_WITH_PHONE'),
                                Constants::CONFIRM_TYPE_EMAIL => Loc::getMessage('FIRSTBIT_APPOINTMENT_CONFIRM_WITH_EMAIL')
                            ]
                        ]
                    ],
                    [ 'note' => Loc::getMessage('FIRSTBIT_APPOINTMENT_CONFIRM_WITH_NOTE')],
                ]
            ],
            [
                'DIV'       => "view_tab",
                'TAB'       => Loc::getMessage("FIRSTBIT_APPOINTMENT_TAB_VIEW"),
                'ICON'      => '',
                'TITLE'     => Loc::getMessage("FIRSTBIT_APPOINTMENT_TAB_TITLE_VIEW"),
                'OPTIONS'   => [
                    Loc::getMessage("FIRSTBIT_APPOINTMENT_LOGO_UPLOAD"),
                    [
                        'appointment_view_logo_image',
                        Loc::getMessage("FIRSTBIT_APPOINTMENT_LOGO_UPLOAD"),
                        "",
                        ['file']
                    ],
                    Loc::getMessage("FIRSTBIT_APPOINTMENT_MAIN_BTN_SETTINGS"),
                    [
                        'appointment_view_use_custom_main_btn',
                        Loc::getMessage("FIRSTBIT_APPOINTMENT_USE_CUSTOM_MAIN_BTN"),
                        "N",
                        ['checkbox']
                    ],
                    [
                        'appointment_view_custom_main_btn_id',
                        Loc::getMessage("FIRSTBIT_APPOINTMENT_CUSTOM_BTN_ID"),
                        "",
                        ['text', "50"]
                    ],
                    [
                        '--appointment-start-btn-bg-color',
                        Loc::getMessage("FIRSTBIT_APPOINTMENT_MAIN_BTN_BG_COLOR"),
                        "#025ea1",
                        ['colorPicker']
                    ],
                    [
                        '--appointment-start-btn-text-color',
                        Loc::getMessage("FIRSTBIT_APPOINTMENT_MAIN_BTN_TEXT_COLOR"),
                        "#fff",
                        ['colorPicker']
                    ],

                    Loc::getMessage("FIRSTBIT_APPOINTMENT_FORM_COLORS_SETTINGS"),
                    [
                        '--appointment-main-color',
                        Loc::getMessage("FIRSTBIT_APPOINTMENT_FORM_COLOR_MAIN"),
                        "#025ea1",
                        ['colorPicker']
                    ],
                    [
                        '--appointment-field-color',
                        Loc::getMessage("FIRSTBIT_APPOINTMENT_FORM_COLOR_FIELD"),
                        "#1B3257",
                        ['colorPicker']
                    ],
                    [
                        '--appointment-form-text-color',
                        Loc::getMessage("FIRSTBIT_APPOINTMENT_FORM_COLOR_TEXT"),
                        "#f5f5f5",
                        ['colorPicker']
                    ],
                    [
                        '--appointment-btn-bg-color',
                        Loc::getMessage("FIRSTBIT_APPOINTMENT_FORM_COLOR_BTN"),
                        "#12b1e3",
                        ['colorPicker']
                    ],
                    [
                        '--appointment-btn-text-color',
                        Loc::getMessage("FIRSTBIT_APPOINTMENT_FORM_COLOR_BTN_TEXT"),
                        "#ffffff",
                        ['colorPicker']
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
                        if ($optionName === 'appointment_view_logo_image')
                        {
                            $moduleId = Constants::APPOINTMENT_MODULE_ID;
                            $currentValue = Option::get($moduleId, $optionName);
                            $optionValue = $this->request->getFile($optionName);

                            if (empty($optionValue['name']) && !empty($currentValue)){
                                continue;
                            }

                            $arImage = $optionValue;
                            $arImage["MODULE_ID"] = $moduleId;

                            if (strlen($arImage["name"]) > 0)
                            {
                                $fid = CFile::SaveFile(
                                        $arImage, $arImage["MODULE_ID"],
                                    false, false, '', false
                                );
                                $optionValue = (int)$fid > 0 ? $fid : '';
                            }
                        }
                        Option::set(
                            $this->moduleId,
                            $optionName,
                            is_array($optionValue) ? json_encode($optionValue) : $optionValue
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
        <form method="POST" action="<?=$this->formAction?>" name="firstbit_appointment_settings" enctype="multipart/form-data">
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
     * @param string $module_id
     * @param $option
     */
    protected function drawSettingsRow(string $module_id, $option)
    {
        if(empty($option))return;

        if(!is_array($option))
        {
            echo "<tr class='heading'><td colspan='2'>$option</td></tr>";
        }
        elseif(isset($option["note"]))
        {
            echo    "<tr>
                        <td colspan='2'>
                            <div class='adm-info-message-wrap'>
                                <div class='adm-info-message'>{$option["note"]}</div>
                            </div>
                        </td>
                    </tr>";
        }
        else
        {
            $currentVal = Option::get($module_id, $option[0], $option[2]);
            echo "<tr>";
            $this->renderTitle($option[1]);
            $this->renderInput($option, $currentVal ?? '');
            echo "</tr>";
        }
    }

    protected function drawSettingsList(string $module_id, array $arParams)
    {
        foreach($arParams as $Option)
        {
            $this->drawSettingsRow($module_id, $Option);
        }
    }

    protected function renderTitle(string $text)
    {
        echo "<td><span>$text</span></td>";
    }

    protected function renderInput(array $option, string $val)
    {
        $name  = $option[0];
        $type  = $option[3];
        ?>
        <td style="width: 50%">
            <label for="<?=$name?>" class="firstbit-appointment-adm-label">
                <?
                switch ($type[0])
                {
                    case "checkbox":
                        $checked = ($val === "Y") ? "checked" : '';
                        echo "<input type='checkbox' id='$name' name='$name' value='Y' $checked>";
                        break;
                    case "text":
                    case "password":
                        $autocomplete = $type[0] === 'password' ? 'autocomplete="new-password"' : '';
                        echo "<input type='$type[0]' id='$name' name='$name' value='$val' size='$type[1]' maxlength='255' $autocomplete>";
                        break;
                    case "select":
                        $arr = is_array($type[1]) ? $type[1] : [];
                        echo "<select name='$name'>";
                        foreach($arr as $optionVal => $displayVal)
                        {
                            $selected = ($val === $optionVal) ? "selected" : '';
                            echo "<option value='$optionVal' $selected>$displayVal</option>";
                        }
                        echo "</select>";
                        break;
                    case "multiselect":
                        $arr = is_array($type[1]) ? $type[1] : [];
                        $name .= '[]';
                        $arr_val = json_decode($val);
                        echo "<select name='$name' size='5' multiple>";
                        foreach($arr as $optionVal => $displayVal)
                        {
                            $selected = (in_array($optionVal, $arr_val)) ? "selected" : '';
                            echo "<option value='$optionVal' $selected>$displayVal</option>";
                        }
                        echo "</select>";
                        break;
                    case "textarea":
                        echo "<textarea rows='$type[1]' cols='$type[2]' name='$name'>$val</textarea>";
                        break;
                    case "staticText":
                        echo "<span>$val</span>";
                        break;
                    case "colorPicker":
                        echo "<input type='text' id='$name' name='$name' value='$val' readonly/>
                              <script>
                                BX.ready(function() {
                                    BX.FirstBit.Appointment.Admin.bindColorPickerToNode('$name', '$name', '$option[2]');
                                });
                              </script>";
                        break;
                    case "file":
                        if (is_numeric($val) && (int)$val > 0){
                            $link = CFile::GetPath($val);
                            if (!empty($link)){
                                echo "<div><img src='$link' alt='logo' width='200'></div>";
                            }
                        }
                        echo "<input type='file' id='$name' name='$name'/>";
                        break;
                }
                ?>
            </label>
            <script>
                BX.ready(() => BX.FirstBit.Appointment.Admin.activateInputs());
            </script>
        </td><?
    }
}