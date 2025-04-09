<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 08.03.2025
 * ==================================================
*/
namespace ANZ\Appointment\Admin\Page\Other;

use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use ANZ\Appointment\Admin\Page\BaseAdminPage;

class AppointmentListPage extends BaseAdminPage
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = Loc::getMessage('ANZ_ADMIN_LIST_PAGE_TITLE');
    }

    public function draw(): void
    {
        $this->globalApp->IncludeComponent('anz:appointment.list', '');
    }
}