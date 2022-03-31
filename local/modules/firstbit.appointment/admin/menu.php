<?php
use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

EventManager::getInstance()->addEventHandlerCompatible(
    'main', 'OnBuildGlobalMenu',
    function(&$arGlobalMenu, &$arModuleMenu)
    {
        if (!defined('FIRSTBIT_APPOINTMENT_MENU_INCLUDED')) {
            define('FIRSTBIT_APPOINTMENT_MENU_INCLUDED', true);

            $moduleID = 'firstbit.appointment';

            // TODO add css to installed files
            $GLOBALS['APPLICATION']->SetAdditionalCss("/bitrix/css/" . $moduleID . "/menu.css");

            if ($GLOBALS['APPLICATION']->GetGroupRight($moduleID) >= 'R') {
                $arMenu = array(
                    'menu_id' => 'global_menu_firstbit_appointment',
                    'text' => 'Первый бит. Запись на приём',
                    'title' => 'Первый бит. Запись на приём',
                    'sort' => 1000,
                    'items_id' => 'global_menu_firstbit_appointment_items',
                    'icon' => 'firstbit_appointment_icon',
                    'items' => array(
                        array(
                            'text' => 'Cписок записей текст',
                            'title' => 'Список записей title',
                            'sort' => 10,
                            'url' => '/bitrix/admin/firstbit.app.list.page.php?lang=' . urlencode(LANGUAGE_ID),
                            'icon' => 'firstbit_appointment_control_center',
                            'page_icon' => 'firstbit_appointment_page_control_center',
                            'items_id' => 'firstbit_appointment_settings_control_center',
                        ),
                        array(
                            'text' => 'Настройки',
                            'title' => 'Настройки',
                            'sort' => 60,
                            'icon' => 'firstbit_appointment_settings',
                            'page_icon' => 'firstbit_appointment_settings_page_icon',
                            'items_id' => 'firstbit_appointment_settings',
                            "items" => array(
                                array(
                                    'text' => 'Настройки 1',
                                    'title' => 'Настройки 1',
                                    'sort' => 10,
                                    'url' => '/bitrix/admin/settings.php?mid='.$moduleID.'&mid_menu=1&lang='.urlencode(LANGUAGE_ID),
                                    'icon' => '',
                                    'page_icon' => 'firstbit_appointment_settings_page_icon_1',
                                    'items_id' => 'firstbit_appointment_settings_page_1',
                                ),
                                array(
                                    'text' => 'Настройки 2',
                                    'title' => 'Настройки 2',
                                    'sort' => 10,
                                    'url' => '/bitrix/admin/settings.php?mid='.$moduleID.'&mid_menu=1&lang='.urlencode(LANGUAGE_ID),
                                    'icon' => '',
                                    'page_icon' => 'firstbit_appointment_settings_page_icon_2',
                                    'items_id' => 'firstbit_appointment_settings_page_2',
                                ),
                            )
                        ),
                    ),
                );

                if (!isset($arGlobalMenu['global_menu_firstbit'])) {
                    $arGlobalMenu['global_menu_firstbit'] = array(
                        'menu_id' => 'global_menu_firstbit',
                        'text' => 'Первый Бит',
                        'title' => 'Первый Бит',
                        'sort' => 1000,
                        'items_id' => 'global_menu_firstbit_items',
                    );
                }

                $arGlobalMenu['global_menu_firstbit']['items'][$moduleID] = $arMenu;
            }
        }
    }
);

