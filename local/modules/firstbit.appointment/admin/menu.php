<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - menu.php
 * 10.07.2022 22:37
 * ==================================================
 */

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
        if (!defined('ANZ_APPOINTMENT_MENU_INCLUDED')) {
            define('ANZ_APPOINTMENT_MENU_INCLUDED', true);

            $moduleID        = 'anz.appointment';
            $vendorName      = 'anz';
            $moduleNameShort = 'appointment';

            $GLOBALS['APPLICATION']->SetAdditionalCss("/bitrix/css/".$vendorName."/".$moduleNameShort. "/menu.css");

            if ($GLOBALS['APPLICATION']->GetGroupRight($moduleID) >= 'R')
            {
                $arMenu = array(
                    'menu_id' => 'global_menu_anz_appointment',
                    'text' => Loc::getMessage('ANZ_APPOINTMENT_MENU_MAIN_TITLE'),
                    'title' => Loc::getMessage('ANZ_APPOINTMENT_MENU_MAIN_TITLE'),
                    'sort' => 1000,
                    'items_id' => 'global_menu_anz_appointment_items',
                    'icon' => 'ui-icon ui-icon-service-site-b24 ui-icon-sm anz_appointment_main_icon',
                    'items' => array(
                        array(
                            'text' => Loc::getMessage('ANZ_APPOINTMENT_MENU_LIST_TITLE'),
                            'title' => Loc::getMessage('ANZ_APPOINTMENT_MENU_LIST_TITLE'),
                            'sort' => 10,
                            'url' => '/bitrix/admin/anz.app.list.page.php?lang=' . urlencode(LANGUAGE_ID),
                            'icon' => 'ui-icon ui-icon-service-webform ui-icon-sm anz_appointment_list_menu_icon',
                            'page_icon' => 'anz_appointment_list_page_icon',
                        ),
                        array(
                            'text' => Loc::getMessage('ANZ_APPOINTMENT_MENU_SETTINGS_TITLE'),
                            'title' => Loc::getMessage('ANZ_APPOINTMENT_MENU_SETTINGS_TITLE'),
                            'sort' => 60,
                            'url' => '/bitrix/admin/anz.app.settings.page.php?lang=' . urlencode(LANGUAGE_ID),
                            'icon' => 'ui-icon ui-icon-service-wheel ui-icon-sm anz_appointment_settings_menu_icon',
                            'page_icon' => 'anz_appointment_settings_page_icon',
                       ),
                    ),
                );

                if (!isset($arGlobalMenu['global_menu_anz'])) {
                    $arGlobalMenu['global_menu_anz'] = array(
                        'menu_id' => 'global_menu_anz',
                        'text' => Loc::getMessage('ANZ_APPOINTMENT_MENU_GLOBAL_TITLE'),
                        'title' => Loc::getMessage('ANZ_APPOINTMENT_MENU_GLOBAL_TITLE'),
                        'sort' => 1000,
                        'icon' => 'anz_appointment_global_menu_icon',
                        'items_id' => 'global_menu_anz_items',
                    );
                }

                $arGlobalMenu['global_menu_anz']['items'][$moduleID] = $arMenu;
            }
        }
    }
);

