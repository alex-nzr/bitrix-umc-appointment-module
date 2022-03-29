<?php
namespace FirstBit\Appointment\Config;

use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Request;
use CAdminTabControl;

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
            'mid'  => htmlspecialcharsbx($this->request->get('mid')),
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
                ]
            ],
            [
                'DIV'   => "edit2",
                'TAB'   => Loc::getMessage("MAIN_TAB_RIGHTS"),
                'ICON'  => '',
                'TITLE' => Loc::getMessage("MAIN_TAB_TITLE_RIGHTS"),
            ]
        ];
    }

    /**
     * @return void
     */
    public function processRequest(): void
    {
        if ($this->request->isPost() && $this->request->getPost('Update') && check_bitrix_sessid())
        {
            foreach ($this->tabs as $arTab)
            {
                __AdmSettingsSaveOptions($this->moduleId, $arTab['OPTIONS']);
            }
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
                    __AdmSettingsDrawList($this->moduleId, $arTab['OPTIONS']);
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
}