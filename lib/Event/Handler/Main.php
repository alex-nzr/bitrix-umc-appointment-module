<?php
namespace ANZ\Appointment\Event\Handler;

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
