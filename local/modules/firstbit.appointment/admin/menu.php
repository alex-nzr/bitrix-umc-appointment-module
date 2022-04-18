<?php
use Bitrix\Main\EventManager;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;

Loc::loadMessages(__FILE__);
try {
    Extension::load("ui.icons");
} catch (LoaderException $e) {
}

EventManager::getInstance()->addEventHandlerCompatible(
    'main', 'OnBuildGlobalMenu',
    function(&$arGlobalMenu, &$arModuleMenu)
    {
        if (!defined('FIRSTBIT_APPOINTMENT_MENU_INCLUDED')) {
            define('FIRSTBIT_APPOINTMENT_MENU_INCLUDED', true);

            $moduleID        = 'firstbit.appointment';
            $vendorName      = 'firstbit';
            $moduleNameShort = 'appointment';

            $GLOBALS['APPLICATION']->SetAdditionalCss("/bitrix/css/".$vendorName."/".$moduleNameShort. "/menu.css");

            if ($GLOBALS['APPLICATION']->GetGroupRight($moduleID) >= 'R')
            {
                $arMenu = array(
                    'menu_id' => 'global_menu_firstbit_appointment',
                    'text' => Loc::getMessage('FIRSTBIT_APPOINTMENT_MENU_MAIN_TITLE'),
                    'title' => Loc::getMessage('FIRSTBIT_APPOINTMENT_MENU_MAIN_TITLE'),
                    'sort' => 1000,
                    'items_id' => 'global_menu_firstbit_appointment_items',
                    'icon' => 'ui-icon ui-icon-service-site-b24 ui-icon-sm firstbit_appointment_main_icon',
                    'items' => array(
                        array(
                            'text' => Loc::getMessage('FIRSTBIT_APPOINTMENT_MENU_LIST_TITLE'),
                            'title' => Loc::getMessage('FIRSTBIT_APPOINTMENT_MENU_LIST_TITLE'),
                            'sort' => 10,
                            'url' => '/bitrix/admin/firstbit.app.list.page.php?lang=' . urlencode(LANGUAGE_ID),
                            'icon' => 'ui-icon ui-icon-service-webform ui-icon-sm firstbit_appointment_list_menu_icon',
                            'page_icon' => 'firstbit_appointment_list_page_icon',
                        ),
                        array(
                            'text' => Loc::getMessage('FIRSTBIT_APPOINTMENT_MENU_SETTINGS_TITLE'),
                            'title' => Loc::getMessage('FIRSTBIT_APPOINTMENT_MENU_SETTINGS_TITLE'),
                            'sort' => 60,
                            'url' => '/bitrix/admin/firstbit.app.settings.page.php?lang=' . urlencode(LANGUAGE_ID),
                            'icon' => 'ui-icon ui-icon-service-wheel ui-icon-sm firstbit_appointment_settings_menu_icon',
                            'page_icon' => 'firstbit_appointment_settings_page_icon',
                       ),
                    ),
                );

                if (!isset($arGlobalMenu['global_menu_firstbit'])) {
                    $arGlobalMenu['global_menu_firstbit'] = array(
                        'menu_id' => 'global_menu_firstbit',
                        'text' => Loc::getMessage('FIRSTBIT_APPOINTMENT_MENU_GLOBAL_TITLE'),
                        'title' => Loc::getMessage('FIRSTBIT_APPOINTMENT_MENU_GLOBAL_TITLE'),
                        'sort' => 1000,
                        'icon' => 'firstbit_appointment_global_menu_icon',
                        'items_id' => 'global_menu_firstbit_items',
                    );
                }

                $arGlobalMenu['global_menu_firstbit']['items'][$moduleID] = $arMenu;
            }
        }
    }
);

