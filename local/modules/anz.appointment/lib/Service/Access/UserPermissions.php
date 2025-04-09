<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 15.03.2025
 * ==================================================
*/
namespace ANZ\Appointment\Service\Access;

use ANZ\Appointment\Internals\ServiceManager;
use CMain;
use CUser;

class UserPermissions
{
    protected CMain $App;

    public function __construct()
    {
        $this->App = $GLOBALS['APPLICATION'];
    }

    public function checkReadPermissions(): bool
    {
        return $this->App->GetGroupRight(ServiceManager::getModuleId()) >= "R";
    }

    public function checkOptionsWritePermissions(): bool
    {
        return $this->App->GetGroupRight(ServiceManager::getModuleId()) === "W";
    }

    public function isAdmin(): bool
    {
        if (!$GLOBALS['USER'] instanceof CUser)
        {
            $GLOBALS['USER'] = new CUser;
        }
        return $GLOBALS['USER']->IsAdmin();
    }
}