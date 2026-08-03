<?php
namespace ANZ\Appointment\Admin\Page\Appointment;

use Bitrix\Main\Localization\Loc;
use ANZ\Appointment\Admin\Page\BaseAdminPage;

class ListPage extends BaseAdminPage
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = Loc::getMessage('ANZ_ADMIN_LIST_PAGE_TITLE') ?? 'empty page title';
    }

    public function draw(): void
    {
        $this->globalApp->IncludeComponent('anz:appointment.list', '');
    }
}
