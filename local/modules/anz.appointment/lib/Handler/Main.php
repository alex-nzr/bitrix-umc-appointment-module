<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 09.12.2022
 * ==================================================
*/
namespace ANZ\Appointment\Handler;

use ANZ\Appointment\Admin;

class Main
{
    /**
     * @throws \Exception
     */
    public static function onBuildGlobalMenu(&$globalAdminMenu, &$arModuleMenu): void
    {
        if (!defined('ANZ_APPOINTMENT_MENU_INCLUDED'))
        {
            if (!is_array($globalAdminMenu))
            {
                $globalAdminMenu = [];
            }

            if (!is_array($arModuleMenu))
            {
                $arModuleMenu = [];
            }

            (new Admin\Menu\Manager)->processGlobalMenu($globalAdminMenu);

            define('ANZ_APPOINTMENT_MENU_INCLUDED', true);
        }
    }
}