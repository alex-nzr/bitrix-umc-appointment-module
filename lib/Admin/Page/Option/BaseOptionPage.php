<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 08.03.2025
 * ==================================================
*/
namespace ANZ\Appointment\Admin\Page\Option;

use ANZ\Appointment\Admin\Page\BaseAdminPage;
use ANZ\Appointment\Core\Contract\Option\IOptionStorage;
use Exception;

abstract class BaseOptionPage extends BaseAdminPage
{
    protected IOptionStorage $optionStorage;

    public function __construct(IOptionStorage $optionStorage)
    {
        parent::__construct();
        $this->optionStorage = $optionStorage;
    }

    public function draw(): void
    {
        try
        {
            if ($this->checkAccess())
            {
                $this->globalApp->IncludeComponent('anz:admin.options', '', [
                    'PAGE_TITLE' => $this->pageTitle,
                    'TABS' => $this->optionStorage->getTabs()
                ]);
            }
        }
        catch (Exception $e)
        {
            ShowError($e->getMessage());
        }
    }
}