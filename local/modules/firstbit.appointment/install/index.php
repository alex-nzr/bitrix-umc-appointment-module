<?php

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\EventManager;
use Bitrix\Main\IO\Directory as Dir;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class firstbit_appointment extends CModule
{
    private CMain $App;
    private ?string $docRoot;
    private $parnterId;

    public function __construct(){
        $this->App = $GLOBALS['APPLICATION'];
        $this->docRoot = Application::getDocumentRoot();
        $this->parnterId = explode("_", get_class($this))[0];

        $arModuleVersion = [];
        include(__DIR__."/version.php");

        $this->MODULE_ID            = str_replace("_", ".", get_class($this));
        $this->MODULE_VERSION       = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE  = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME          = Loc::getMessage("FIRSTBIT_APPOINTMENT_MODULE_NAME");
        $this->MODULE_DESCRIPTION   = Loc::getMessage("FIRSTBIT_APPOINTMENT_MODULE_DESCRIPTION");
        $this->PARTNER_NAME         = Loc::getMessage("FIRSTBIT_APPOINTMENT_PARTNER_NAME");
        $this->PARTNER_URI          = Loc::getMessage("FIRSTBIT_APPOINTMENT_PARTNER_URI");
        $this->MODULE_SORT          = 1;
        $this->MODULE_GROUP_RIGHTS  = "Y";
        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = "Y";
    }

    public function DoInstall()
    {
        if($this->isSupportD7())
        {
            $this->InstallDB();
            $this->InstallEvents();
            $this->InstallFiles();

            ModuleManager::registerModule($this->MODULE_ID);
        }
        else{
            $this->App->ThrowException(
                Loc::getMessage("FIRSTBIT_APPOINTMENT_INSTALL_ERROR")
            );
        }

        $this->App->IncludeAdminFile(
            Loc::getMessage("FIRSTBIT_APPOINTMENT_INSTALL_TITLE"),
            __DIR__."/step.php"
        );
    }

    public function DoUninstall()
    {
        $request = Context::getCurrent()->getRequest();

        if ($request->get('step') < 2)
        {
            $this->App->IncludeAdminFile(
                Loc::getMessage("FIRSTBIT_APPOINTMENT_UNINSTALL_TITLE"),
                __DIR__."/unStep_1.php"
            );
        }
        else
        {
            ModuleManager::unRegisterModule($this->MODULE_ID);
            $this->UnInstallFiles();
            $this->UnInstallEvents();
            if ($request->get('saveData') !== "Y"){
                $this->UnInstallDB();
            }
            $this->App->IncludeAdminFile(
                Loc::getMessage("FIRSTBIT_APPOINTMENT_UNINSTALL_TITLE"),
                __DIR__."/unStep_2.php"
            );
        }
    }

    public function InstallDB()
    {

    }

    public function UnInstallDB()
    {
        try {
            Option::delete($this->MODULE_ID);
        }catch(Exception $e){
            $this->App->ThrowException(Loc::getMessage("FIRSTBIT_APPOINTMENT_UNINSTALL_ERROR"));
        }
    }

    public function InstallEvents()
    {
        EventManager::getInstance()->registerEventHandler($this->MODULE_ID, 'TestEventD7', $this->MODULE_ID, '\Academy\D7\Event', 'eventHandler');
    }

    public function UnInstallEvents()
    {
        EventManager::getInstance()->unRegisterEventHandler($this->MODULE_ID, 'TestEventD7', $this->MODULE_ID, '\Academy\D7\Event', 'eventHandler');
    }

    public function InstallFiles()
    {
        CopyDirFiles(__DIR__.'/admin/', $this->docRoot.'/bitrix/admin', true);
        CopyDirFiles(__DIR__.'/components/', $this->docRoot.'/bitrix/components', true, true);
    }

    public function UnInstallFiles()
    {
        DeleteDirFiles(__DIR__.'/admin/', $this->docRoot.'/bitrix/admin');
        Dir::deleteDirectory($this->docRoot . '/bitrix/components/'.$this->parnterId.'/');
    }

    private function isSupportD7(): bool
    {
        return CheckVersion(ModuleManager::getVersion("main"), "14.00.00");
    }
}