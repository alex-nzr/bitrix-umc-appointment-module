<?php
namespace ANZ\Appointment\Service\Access;

use ANZ\Appointment\Config\Configuration;
use CMain;
use CUser;

class UserPermissions
{
    protected CMain $App;
    protected CUser $globalUser;
    protected int $userId;

    public function __construct()
    {
        $this->App = $GLOBALS['APPLICATION'];
        if (!($GLOBALS['USER'] instanceof CUser))
        {
            $GLOBALS['USER'] = new CUser;
        }
        $this->globalUser = $GLOBALS['USER'];
        $this->userId = (int)$this->globalUser->GetID();
    }

    /**
     * @throws \ANZ\Appointment\Core\Exception\ConfigurationException
     */
    public function checkReadPermissions(): bool
    {
        return $this->App->GetGroupRight(Configuration::getModuleId()) >= "R";
    }

    /**
     * @throws \ANZ\Appointment\Core\Exception\ConfigurationException
     */
    public function checkOptionsWritePermissions(): bool
    {
        return $this->App->GetGroupRight(Configuration::getModuleId()) === "W";
    }

    public function isAdmin(): bool
    {
        return $this->globalUser->IsAdmin();
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
