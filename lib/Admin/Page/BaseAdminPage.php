<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 08.03.2025
 * ==================================================
*/
namespace ANZ\Appointment\Admin\Page;

use ANZ\Appointment\Core\Contract\Page\IPage;
use Bitrix\Main\AccessDeniedException;
use CMain;
use CUser;

abstract class BaseAdminPage implements IPage
{
    protected CMain $globalApp;
    protected string $pageTitle = 'Admin page';

    public function __construct()
    {
        $this->globalApp = ($GLOBALS['APPLICATION'] instanceof CMain) ? $GLOBALS['APPLICATION'] : new CMain;
    }

    abstract public function draw();

    /**
     * @return bool
     */
    public function isAdminPage(): bool
    {
        return true;
    }

    /**
     * @return bool
     * @throws \Bitrix\Main\AccessDeniedException
     */
    public function checkAccess(): bool
    {
        if (!($GLOBALS['USER'] instanceof CUser) || !$GLOBALS['USER']->IsAdmin())
        {
            throw new AccessDeniedException();
        }

        return true;
    }
}