<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - OptionManager.php
 * 10.07.2022 22:37
 * ==================================================
 */
namespace ANZ\Appointment\Config;

use Bitrix\Main\Localization\Loc;

/**
 * @class OptionList
 * @package ANZ\Appointment\Config
 */
class OptionList extends BaseOptionManager
{
    /**
     * @param string $moduleId
     * @throws \Exception
     */
    public function __construct(string $moduleId)
    {
        parent::__construct($moduleId);
        Loc::loadMessages(__FILE__);
    }

    /**
     * @return void
     */
    protected function setTabs(): void
    {
        $this->tabs = [
            [
                'DIV'   => "settings_tab",
                'TAB'   => Loc::getMessage("ANZ_APPOINTMENT_MODULE_SETTINGS"),
                'ICON'  => '',
                'TITLE' => Loc::getMessage("ANZ_APPOINTMENT_MODULE_SETTINGS"),
                "OPTIONS" => [
                    Loc::getMessage("ANZ_APPOINTMENT_API_SETTINGS"),
                    [
                        Constants::OPTION_KEY_API_WS_URL,
                        Loc::getMessage("ANZ_APPOINTMENT_API_ADDRESS"),
                        "http://localhost:3500/umc_corp/ws/ws1.1cws?wsdl",
                        ['text', 50]
                    ],
                    [
                        Constants::OPTION_KEY_API_WS_LOGIN,
                        Loc::getMessage("ANZ_APPOINTMENT_API_LOGIN"),
                        "siteIntegration",
                        ['text', 50]
                    ],
                    [
                        Constants::OPTION_KEY_API_WS_PASSWORD,
                        Loc::getMessage("ANZ_APPOINTMENT_API_PASSWORD"),
                        "123456",
                        ['text', 50]
                    ],

                    Loc::getMessage("ANZ_APPOINTMENT_USE_AUTO_INJECTING"),
                    [
                        Constants::OPTION_KEY_AUTO_INC,
                        Loc::getMessage('ANZ_APPOINTMENT_USE_AUTO_INJECTING_ON'),
                        "N",
                        ['checkbox']
                    ],
                    [ 'note' => Loc::getMessage('ANZ_APPOINTMENT_USE_AUTO_INJECTING_NOTE')],

                    Loc::getMessage("ANZ_APPOINTMENT_OTHER_SETTINGS"),
                    [
                        Constants::OPTION_KEY_SCHEDULE_DAYS,
                        Loc::getMessage('ANZ_APPOINTMENT_SCHEDULE_PERIOD'),
                        Constants::DEFAULT_SCHEDULE_PERIOD_DAYS,
                        ['text', 5]
                    ],
                    [
                        Constants::OPTION_KEY_DEFAULT_DURATION,
                        Loc::getMessage('ANZ_APPOINTMENT_DEFAULT_DURATION'),
                        Constants::DEFAULT_APPOINTMENT_DURATION_SEC,
                        ['text', 5]
                    ],
                    [
                        Constants::OPTION_KEY_USE_NOMENCLATURE,
                        Loc::getMessage('ANZ_APPOINTMENT_USE_NOMENCLATURE'),
                        "N",
                        ['checkbox']
                    ],
                    [ 'note' => Loc::getMessage('ANZ_APPOINTMENT_USE_NOMENCLATURE_WARNING')],

                    /*[
                        Constants::OPTION_KEY_DOCTOR_BEFORE_SERVICE,
                        Loc::getMessage('ANZ_APPOINTMENT_SELECT_DOCTOR_BEFORE_SERVICE'),
                        "N",
                        ['checkbox']
                    ],
                    [ 'note' => Loc::getMessage('ANZ_APPOINTMENT_SELECT_DOCTOR_BEFORE_SERVICE_NOTE')],*/

                    [
                        Constants::OPTION_KEY_USE_TIME_STEPS,
                        Loc::getMessage('ANZ_APPOINTMENT_USE_TIME_STEPS'),
                        "N",
                        ['checkbox']
                    ],
                    [
                        Constants::OPTION_KEY_TIME_STEP_DURATION,
                        Loc::getMessage('ANZ_APPOINTMENT_TIME_STEP_DURATION'),
                        "15",
                        ['text', 5]
                    ],
                    [ 'note' => Loc::getMessage('ANZ_APPOINTMENT_USE_TIME_STEPS_NOTE')],

                    [
                        Constants::OPTION_KEY_STRICT_RELATIONS,
                        Loc::getMessage('ANZ_APPOINTMENT_STRICT_CHECKING_RELATIONS'),
                        "N",
                        ['checkbox']
                    ],
                    [ 'note' => Loc::getMessage('ANZ_APPOINTMENT_STRICT_CHECKING_RELATIONS_NOTE')],

                    [
                        Constants::OPTION_KEY_ALLOW_DOCTOR_WITHOUT_DPT,
                        Loc::getMessage('ANZ_APPOINTMENT_SHOW_DOCTORS_WITHOUT_DEPARTMENT'),
                        "N",
                        ['checkbox']
                    ],
                    [ 'note' => Loc::getMessage('ANZ_APPOINTMENT_SHOW_DOCTORS_WITHOUT_DEPARTMENT_NOTE')],

                    [
                        Constants::OPTION_KEY_USE_WAIT_LIST,
                        Loc::getMessage('ANZ_APPOINTMENT_USE_WAITING_LIST'),
                        "N",
                        ['checkbox']
                    ],
                    [ 'note' => Loc::getMessage('ANZ_APPOINTMENT_USE_WAITING_LIST_NOTE')],

                    [
                        Constants::OPTION_KEY_EMAIL_NOTE,
                        Loc::getMessage('ANZ_APPOINTMENT_USE_EMAIL_NOTE'),
                        "N",
                        ['checkbox']
                    ],

                    [
                        Constants::OPTION_KEY_PRIVACY_PAGE,
                        Loc::getMessage('ANZ_APPOINTMENT_PRIVACY_PAGE_URL'),
                        "#",
                        ['text', 50]
                    ],

                    [
                        Constants::OPTION_KEY_CONFIRM_MODE,
                        Loc::getMessage('ANZ_APPOINTMENT_CONFIRM_WITH'),
                        Constants::CONFIRM_TYPE_NONE,
                        [
                            'select',
                            [
                                Constants::CONFIRM_TYPE_NONE  => Loc::getMessage('ANZ_APPOINTMENT_CONFIRM_WITH_NONE'),
                                Constants::CONFIRM_TYPE_PHONE => Loc::getMessage('ANZ_APPOINTMENT_CONFIRM_WITH_PHONE'),
                                Constants::CONFIRM_TYPE_EMAIL => Loc::getMessage('ANZ_APPOINTMENT_CONFIRM_WITH_EMAIL')
                            ]
                        ]
                    ],
                    [ 'note' => Loc::getMessage('ANZ_APPOINTMENT_CONFIRM_WITH_NOTE')],

                    Loc::getMessage("ANZ_APPOINTMENT_USE_DEMO_MODE"),
                    [
                        Constants::OPTION_KEY_DEMO_MODE,
                        Loc::getMessage('ANZ_APPOINTMENT_USE_DEMO_MODE_ON'),
                        "N",
                        ['checkbox']
                    ],
                ]
            ],
            [
                'DIV'       => "view_tab",
                'TAB'       => Loc::getMessage("ANZ_APPOINTMENT_TAB_VIEW"),
                'ICON'      => '',
                'TITLE'     => Loc::getMessage("ANZ_APPOINTMENT_TAB_TITLE_VIEW"),
                'OPTIONS'   => [
                    Loc::getMessage("ANZ_APPOINTMENT_LOGO_UPLOAD"),
                    [
                        Constants::OPTION_KEY_LOGO,
                        Loc::getMessage("ANZ_APPOINTMENT_LOGO_UPLOAD"),
                        "",
                        ['file']
                    ],
                    Loc::getMessage("ANZ_APPOINTMENT_MAIN_BTN_SETTINGS"),
                    [
                        Constants::OPTION_KEY_USE_CUSTOM_BTN,
                        Loc::getMessage("ANZ_APPOINTMENT_USE_CUSTOM_MAIN_BTN"),
                        "N",
                        ['checkbox']
                    ],
                    [
                        Constants::OPTION_KEY_CUSTOM_BTN_ID,
                        Loc::getMessage("ANZ_APPOINTMENT_CUSTOM_BTN_ID"),
                        "",
                        ['text', "50"]
                    ],
                    [
                        Constants::OPTION_KEY_MAIN_BTN_BG,
                        Loc::getMessage("ANZ_APPOINTMENT_MAIN_BTN_BG_COLOR"),
                        "#025ea1",
                        ['colorPicker']
                    ],
                    [
                        Constants::OPTION_KEY_MAIN_BTN_TEXT_CLR,
                        Loc::getMessage("ANZ_APPOINTMENT_MAIN_BTN_TEXT_COLOR"),
                        "#fff",
                        ['colorPicker']
                    ],

                    Loc::getMessage("ANZ_APPOINTMENT_FORM_COLORS_SETTINGS"),
                    [
                        Constants::OPTION_KEY_FORM_BG,
                        Loc::getMessage("ANZ_APPOINTMENT_FORM_COLOR_MAIN"),
                        "#025ea1",
                        ['colorPicker']
                    ],
                    [
                        Constants::OPTION_KEY_FIELD_BG,
                        Loc::getMessage("ANZ_APPOINTMENT_FORM_COLOR_FIELD"),
                        "#1B3257",
                        ['colorPicker']
                    ],
                    [
                        Constants::OPTION_KEY_FORM_TEXT_CLR,
                        Loc::getMessage("ANZ_APPOINTMENT_FORM_COLOR_TEXT"),
                        "#f5f5f5",
                        ['colorPicker']
                    ],
                    [
                        Constants::OPTION_KEY_FORM_BTN_BG,
                        Loc::getMessage("ANZ_APPOINTMENT_FORM_COLOR_BTN"),
                        "#12b1e3",
                        ['colorPicker']
                    ],
                    [
                        Constants::OPTION_KEY_FORM_BTN_TEXT_CLR,
                        Loc::getMessage("ANZ_APPOINTMENT_FORM_COLOR_BTN_TEXT"),
                        "#ffffff",
                        ['colorPicker']
                    ],
                ]
            ],
            [
                'DIV'   => "access_tab",
                'TAB'   => Loc::getMessage("ANZ_APPOINTMENT_TAB_RIGHTS"),
                'ICON'  => '',
                'TITLE' => Loc::getMessage("ANZ_APPOINTMENT_TAB_TITLE_RIGHTS"),
            ]
        ];
    }
}