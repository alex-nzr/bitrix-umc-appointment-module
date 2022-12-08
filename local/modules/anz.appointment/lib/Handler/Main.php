<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * Bit.Umc - Bitrix integration - Main.php
 * 09.12.2022 00:35
 * ==================================================
 */
namespace ANZ\Appointment\Handler;

use ANZ\Appointment\Internals\Control\ServiceManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class Main
 * @package ANZ\Appointment\Handler
 */
class Main
{
    /**
     * @param $arGlobalMenu
     * @param $arModuleMenu
     */
    public static function onBuildGlobalMenu(&$arGlobalMenu, &$arModuleMenu)
    {
        if (!defined('ANZ_APPOINTMENT_MENU_INCLUDED'))
        {
            define('ANZ_APPOINTMENT_MENU_INCLUDED', true);

            $moduleID        = ServiceManager::getModuleId();
            $vendorName      = explode('.', $moduleID)[0];
            $moduleNameShort = explode('.', $moduleID)[1];

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
}