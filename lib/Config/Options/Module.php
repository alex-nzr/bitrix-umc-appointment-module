<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 08.03.2025
 * ==================================================
*/
namespace ANZ\Appointment\Config\Options;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Config\Constants;
use ANZ\Appointment\Config\ExchangeMode;
use ANZ\Appointment\Core\Contract\Option\IOptionStorage;
use Bitrix\Main\Localization\Loc;


class Module implements IOptionStorage
{
    /**
     * @return array
     * @throws \Exception
     */
    public function getTabs(): array
    {
        return [
            [
                'DIV'   => "settings_tab",
                'TAB'   => Loc::getMessage("ANZ_APPOINTMENT_MODULE_SETTINGS"),
                'ICON'  => '',
                'TITLE' => Loc::getMessage("ANZ_APPOINTMENT_MODULE_SETTINGS"),
                "OPTIONS" => [
                    [
                        Constants::OPTION_KEY_AUTO_INC,
                        Loc::getMessage('ANZ_APPOINTMENT_USE_AUTO_INJECTING_ON'),
                        "N",
                        ['checkbox']
                    ],

                    [
                        Constants::OPTION_KEY_PRIVACY_PAGE,
                        Loc::getMessage('ANZ_APPOINTMENT_PRIVACY_PAGE_URL'),
                        "#",
                        ['text']
                    ],

                    [
                        Constants::OPTION_KEY_DEMO_MODE,
                        Loc::getMessage('ANZ_APPOINTMENT_USE_DEMO_MODE_ON'),
                        "N",
                        ['checkbox']
                    ],
                ]
            ],
            [
                'DIV'       => "exchange_tab",
                'TAB'       => Loc::getMessage("ANZ_APPOINTMENT_EXCHANGE_SETTINGS"),
                'ICON'      => '',
                'TITLE'     => Loc::getMessage("ANZ_APPOINTMENT_EXCHANGE_SETTINGS"),
                'OPTIONS'   => [
                    Loc::getMessage("ANZ_APPOINTMENT_API_SETTINGS"),
                    [
                        Constants::OPTION_KEY_API_WS_URL,
                        Loc::getMessage("ANZ_APPOINTMENT_API_ADDRESS"),
                        "http://localhost:3500/umc_corp/ws/ws1.1cws?wsdl",
                        ['text']
                    ],
                    [
                        Constants::OPTION_KEY_API_WS_LOGIN,
                        Loc::getMessage("ANZ_APPOINTMENT_API_LOGIN"),
                        "siteIntegration",
                        ['text']
                    ],
                    [
                        Constants::OPTION_KEY_API_WS_PASSWORD,
                        Loc::getMessage("ANZ_APPOINTMENT_API_PASSWORD"),
                        "123456",
                        ['password']
                    ],
                    [
                        'component' => 'anz:appointment.check-api-btn',
                        'template' => '',
                        'params' => []
                    ],

                    Loc::getMessage('ANZ_APPOINTMENT_EXCHANGE_SETTINGS'),
                    [
                        Constants::OPTION_KEY_EXCHANGE_MODE,
                        Loc::getMessage('ANZ_APPOINTMENT_EXCHANGE_MODE'),
                        ExchangeMode::SOAP->value,
                        [
                            'select',
                            'LIST' => [
                                ExchangeMode::SOAP->value => ExchangeMode::SOAP->value,
                                //ExchangeMode::HTTP->value => ExchangeMode::HTTP->value
                            ]
                        ]
                    ],
                    [
                        Constants::OPTION_KEY_EXCHANGE_AGENT_ACTIVE,
                        Loc::getMessage('ANZ_APPOINTMENT_EXCHANGE_AGENT_ACTIVE'),
                        'N',
                        ['checkbox']
                    ],
                    [
                        Constants::OPTION_KEY_EXCHANGE_EXEC_INTERVAL,
                        Loc::getMessage('ANZ_APPOINTMENT_EXCHANGE_EXEC_INTERVAL'),
                        5,
                        ['number']
                    ],
                    [
                        Constants::OPTION_KEY_EXCHANGE_NEXT_EXEC_DATE,
                        Loc::getMessage('ANZ_APPOINTMENT_EXCHANGE_NEXT_EXEC_DATE'),
                        '',
                        ['datetime']
                    ],
                    [
                        Constants::OPTION_KEY_EXCHANGE_SCHEDULE_PERIOD,
                        Loc::getMessage('ANZ_APPOINTMENT_EXCHANGE_SCHEDULE_PERIOD'),
                        14,
                        ['number']
                    ],
                    [
                        Constants::OPTION_KEY_EXCHANGE_CACHE_TTL,
                        Loc::getMessage('ANZ_APPOINTMENT_EXCHANGE_CACHE_TTL'),
                        3600 * 3,
                        ['number']
                    ],
                    [
                        Constants::OPTION_KEY_EXCHANGE_USE_SERVICES,
                        Loc::getMessage('ANZ_APPOINTMENT_EXCHANGE_USE_SERVICES'),
                        'N',
                        ['checkbox']
                    ],

                    [
                        Constants::OPTION_KEY_EXCHANGE_DEFAULT_APPOINTMENT_DURATION,
                        Loc::getMessage('ANZ_APPOINTMENT_EXCHANGE_DEFAULT_APPOINTMENT_DURATION'),
                        1800,
                        ['number']
                    ],

                    [
                        'component' => 'anz:appointment.execute-btn',
                        'template' => '',
                        'params' => []
                    ],
                ],
            ],
            [
                'DIV' => 'appointment_tab',
                'TAB'       => Loc::getMessage("ANZ_APPOINTMENT_APP_SETTINGS"),
                'ICON'      => '',
                'TITLE'     => Loc::getMessage("ANZ_APPOINTMENT_APP_SETTINGS"),
                'OPTIONS' => [
                    [
                        Constants::OPTION_KEY_USE_WAIT_LIST,
                        Loc::getMessage('ANZ_APPOINTMENT_USE_WAITING_LIST'),
                        "N",
                        ['checkbox']
                    ],

                    [
                        Constants::OPTION_KEY_EMAIL_NOTE,
                        Loc::getMessage('ANZ_APPOINTMENT_USE_EMAIL_NOTE'),
                        "N",
                        ['checkbox']
                    ],

                    [
                        Constants::OPTION_KEY_EXCHANGE_CONFIRM_MODE,
                        Loc::getMessage('ANZ_APPOINTMENT_EXCHANGE_CONFIRM_MODE'),
                        Constants::CONFIRM_TYPE_NONE,
                        [
                            'select',
                            'LIST' => [
                                Constants::CONFIRM_TYPE_NONE  => Loc::getMessage('ANZ_APPOINTMENT_EXCHANGE_CONFIRM_NONE'),
                                Constants::CONFIRM_TYPE_PHONE => Loc::getMessage('ANZ_APPOINTMENT_EXCHANGE_CONFIRM_PHONE'),
                                Constants::CONFIRM_TYPE_EMAIL => Loc::getMessage('ANZ_APPOINTMENT_EXCHANGE_CONFIRM_EMAIL')
                            ]
                        ]
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
                        ['file', 'attrs' => ['accept' => 'image/*']]
                    ],

                    Loc::getMessage("ANZ_APPOINTMENT_MAIN_BTN_SETTINGS"),
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
                        ['text']
                    ],

                    Loc::getMessage("ANZ_APPOINTMENT_JS_EXTENSION"),
                    [
                        Constants::OPTION_KEY_JS_EXTENSION,
                        Loc::getMessage('ANZ_APPOINTMENT_JS_EXTENSION_SELECT'),
                        Constants::JS_EXTENSION_BX_POPUP,
                        [
                            'select',
                            'LIST' => [
                                Constants::JS_EXTENSION_BX_POPUP  => Loc::getMessage('ANZ_APPOINTMENT_JS_EXTENSION_BX_POPUP'),
                                Constants::JS_EXTENSION_FORM_VUE => Loc::getMessage('ANZ_APPOINTMENT_JS_EXTENSION_FORM_VUE'),
                            ]
                        ]
                    ],
                    [
                        Constants::OPTION_KEY_CUSTOM_JS_EXTENSION,
                        Loc::getMessage("ANZ_APPOINTMENT_JS_EXTENSION_CUSTOM"),
                        "",
                        ['text']
                    ],

                    Loc::getMessage("ANZ_APPOINTMENT_TEMPLATE_SETTINGS"),
                    [
                        Constants::OPTION_KEY_USE_TIME_STEPS,
                        Loc::getMessage('ANZ_APPOINTMENT_USE_TIME_STEPS'),
                        "N",
                        ['checkbox', 'attrs' => ['data-extension' => Constants::JS_EXTENSION_BX_POPUP]]
                    ],
                    [
                        Constants::OPTION_KEY_TIME_STEP_DURATION,
                        Loc::getMessage('ANZ_APPOINTMENT_TIME_STEP_DURATION'),
                        15,
                        ['number', 'attrs' => ['data-extension' => Constants::JS_EXTENSION_BX_POPUP]]
                    ],
                    [
                        Constants::OPTION_KEY_STRICT_RELATIONS,
                        Loc::getMessage('ANZ_APPOINTMENT_STRICT_CHECKING_RELATIONS'),
                        "N",
                        ['checkbox', 'attrs' => ['data-extension' => Constants::JS_EXTENSION_BX_POPUP]]
                    ],
                    [
                        Constants::OPTION_KEY_ALLOW_DOCTOR_WITHOUT_DPT,
                        Loc::getMessage('ANZ_APPOINTMENT_SHOW_DOCTORS_WITHOUT_DEPARTMENT'),
                        "N",
                        ['checkbox', 'attrs' => ['data-extension' => Constants::JS_EXTENSION_BX_POPUP]]
                    ],

                    Loc::getMessage("ANZ_APPOINTMENT_TEMPLATE_COLORS_SETTINGS"),
                    [
                        Constants::OPTION_KEY_TEMPLATE_MAIN_COLOR,
                        Loc::getMessage("ANZ_APPOINTMENT_TEMPLATE_COLOR_MAIN"),
                        "#025ea1",
                        ['colorPicker']
                    ],
                    [
                        Constants::OPTION_KEY_FIELD_BG,
                        Loc::getMessage("ANZ_APPOINTMENT_FORM_COLOR_FIELD"),
                        "#1B3257",
                        ['colorPicker', 'attrs' => ['data-extension' => Constants::JS_EXTENSION_BX_POPUP]]
                    ],
                    [
                        Constants::OPTION_KEY_FORM_TEXT_CLR,
                        Loc::getMessage("ANZ_APPOINTMENT_FORM_COLOR_TEXT"),
                        "#f5f5f5",
                        ['colorPicker', 'attrs' => ['data-extension' => Constants::JS_EXTENSION_BX_POPUP]]
                    ],
                    [
                        Constants::OPTION_KEY_FORM_BTN_BG,
                        Loc::getMessage("ANZ_APPOINTMENT_FORM_COLOR_BTN"),
                        "#12b1e3",
                        ['colorPicker', 'attrs' => ['data-extension' => Constants::JS_EXTENSION_BX_POPUP]]
                    ],
                    [
                        Constants::OPTION_KEY_FORM_BTN_TEXT_CLR,
                        Loc::getMessage("ANZ_APPOINTMENT_FORM_COLOR_BTN_TEXT"),
                        "#ffffff",
                        ['colorPicker', 'attrs' => ['data-extension' => Constants::JS_EXTENSION_BX_POPUP]]
                    ],
                ]
            ],
            [
                'DIV'       => 'debug_tab',
                'TAB'       => Loc::getMessage("ANZ_APPOINTMENT_DEBUG"),
                'ICON'      => '',
                'TITLE'     => Loc::getMessage("ANZ_APPOINTMENT_DEBUG"),
                'OPTIONS'   => [
                    Loc::getMessage('ANZ_APPOINTMENT_DEBUG'),
                    [
                        Constants::OPTION_KEY_DEBUG_LOGS_TTL,
                        Loc::getMessage('ANZ_APPOINTMENT_DEBUG_LOGS_TTL'),
                        30,
                        ['number']
                    ],
                    [
                        '',
                        Loc::getMessage('ANZ_APPOINTMENT_DEBUG_LOGS_DIR'),
                        '<a href="/bitrix/admin/fileman_admin.php?path='.urlencode(Configuration::getInstance()->getLogFileDir()).'" target="_blank">
                            '.Loc::getMessage('ANZ_APPOINTMENT_DEBUG_LOGS_DIR_LINK').'
                        </a>',
                        ['htmlText']
                    ],
                ]
            ],
            [
                'DIV'   => "access_tab",
                'TAB'   => Loc::getMessage("ANZ_APPOINTMENT_TAB_RIGHTS"),
                'ICON'  => '',
                'TITLE' => Loc::getMessage("ANZ_APPOINTMENT_TAB_TITLE_RIGHTS"),
                'OPTIONS' => [
                    [
                        'group_access' => 'Y'//this option set by bitrix group_rights.php
                    ],
                ]
            ]
        ];
    }
}