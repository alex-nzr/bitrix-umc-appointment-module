<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 08.03.2025
 * ==================================================
*/
namespace ANZ\Appointment\Admin\Page\Option;

use ANZ\Appointment\Core\Contract\Option\IOptionStorage;
use Bitrix\Main\Localization\Loc;

class ModuleSettingsPage extends BaseOptionPage
{
    public function __construct(IOptionStorage $optionStorage)
    {
        parent::__construct($optionStorage);
        $this->pageTitle = Loc::getMessage('ANZ_ADMIN_SETTINGS_PAGE_TITLE');
    }
}