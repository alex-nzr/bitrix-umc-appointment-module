<?php
namespace ANZ\Appointment\Admin\Menu;

use Bitrix\Main\Localization\Loc;
use ANZ\Appointment\Config\Configuration;

class Storage
{
    protected array $additionalMenuStructure;
    protected string $moduleId;

    public function __construct()
    {
        $this->moduleId = Configuration::getModuleId();
        $this->setAdditionalMenuStructure();
    }

    protected function setAdditionalMenuStructure(): void
    {
        $this->additionalMenuStructure = [
            [
                'ID'    => 'anz_appointment',
                'TITLE' => 'ANZ',
                'ITEMS' => [
                    [
                        'TITLE' => Loc::getMessage('ANZ_APPOINTMENT_MENU_MAIN_TITLE'),
                        'ICON' => 'ui-icon ui-icon-service-site-b24 ui-icon-sm anz_appointment_main_icon',
                        'ITEMS' => [
                            [
                                'TITLE' => Loc::getMessage('ANZ_APPOINTMENT_MENU_LIST_TITLE'),
                                'ICON' => 'ui-icon ui-icon-service-webform ui-icon-sm anz_appointment_list_menu_icon',
                                'URL' => '/bitrix/admin/anz.app.list.page.php?lang=' . urlencode(LANGUAGE_ID),
                            ],
                            [
                                'TITLE' => Loc::getMessage('ANZ_APPOINTMENT_MENU_SETTINGS_TITLE'),
                                'ICON' => 'ui-icon ui-icon-service-wheel ui-icon-sm anz_appointment_settings_menu_icon',
                                'URL' => '/bitrix/admin/anz.app.settings.page.php?lang=' . urlencode(LANGUAGE_ID),
                            ],
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function getAdditionalMenuStructure(): array
    {
        return $this->additionalMenuStructure;
    }
}
