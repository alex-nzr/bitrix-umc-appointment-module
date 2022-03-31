<?php

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\EventManager;
use Bitrix\Main\IO\Directory as Dir;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use FirstBit\Appointment\Model\RecordTable;

Loc::loadMessages(__FILE__);

class firstbit_appointment extends CModule
{
    private CMain $App;
    private ?string $docRoot;
    private $partnerId;

    public function __construct(){
        $this->App = $GLOBALS['APPLICATION'];
        $this->docRoot = Application::getDocumentRoot();
        $this->partnerId = explode("_", get_class($this))[0];

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
            ModuleManager::registerModule($this->MODULE_ID);
            $this->InstallDB();
            $this->InstallEvents();
            $this->InstallFiles();
        }
        else{
            $this->App->ThrowException(
                Loc::getMessage("FIRSTBIT_APPOINTMENT_INSTALL_ERROR_VERSION")
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
            $this->UnInstallFiles();
            $this->UnInstallEvents();
            if ($request->get('saveData') !== "Y"){
                $this->UnInstallDB();
            }
            ModuleManager::unRegisterModule($this->MODULE_ID);

            $this->App->IncludeAdminFile(
                Loc::getMessage("FIRSTBIT_APPOINTMENT_UNINSTALL_TITLE"),
                __DIR__."/unStep_2.php"
            );
        }
    }

    public function InstallDB()
    {
        try {
            Loader::includeModule($this->MODULE_ID);

            $connection = Application::getConnection();
            $recordTableName = Base::getInstance(RecordTable::class)->getDBTableName();
            if(!$connection->isTableExists($recordTableName))
            {
                Base::getInstance(RecordTable::class)->createDbTable();
            }
        }
        catch (Exception $e){
            $this->App->ThrowException(
                Loc::getMessage("FIRSTBIT_APPOINTMENT_UNINSTALL_ERROR")." - ". $e->getMessage()
            );
        }
    }

    public function UnInstallDB()
    {
        try {
            Loader::includeModule($this->MODULE_ID);

            Option::delete($this->MODULE_ID);
            $connection = Application::getConnection();
            $recordTableName = Base::getInstance(RecordTable::class)->getDBTableName();
            if($connection->isTableExists($recordTableName))
            {
                $connection->dropTable($recordTableName);
            }
        }
        catch(Exception $e){
            $this->App->ThrowException(
                Loc::getMessage("FIRSTBIT_APPOINTMENT_UNINSTALL_ERROR")." - ". $e->getMessage()
            );
        }
    }

    public function InstallEvents()
    {
        EventManager::getInstance()->registerEventHandler(
            $this->MODULE_ID,
            'TestEventD7',
            $this->MODULE_ID,
            '\Academy\D7\Event',
            'eventHandler'
        );
        EventManager::getInstance()->registerEventHandler(
            'academy.d7',
            '\Academy\D7\Book::OnBeforeAdd',
            $this->MODULE_ID,
            '\Academy\Event\Event',
            'eventHandler'
        );
    }

    public function UnInstallEvents()
    {
        EventManager::getInstance()->unRegisterEventHandler(
            $this->MODULE_ID,
            'TestEventD7',
            $this->MODULE_ID,
            '\Academy\D7\Event',
            'eventHandler'
        );
        EventManager::getInstance()->unRegisterEventHandler(
            'academy.d7',
            '\Academy\D7\Book::OnBeforeAdd',
            $this->MODULE_ID,
            '\Academy\Event\Event',
            'eventHandler'
        );
    }

    public function InstallFiles()
    {
        if (Dir::isDirectoryExists(__DIR__.'/admin/'))
        {
            CopyDirFiles(__DIR__.'/admin/', $this->docRoot.'/bitrix/admin', true);
        }
        if (Dir::isDirectoryExists(__DIR__.'/admin/'))
        {
            CopyDirFiles(__DIR__.'/components/', $this->docRoot.'/bitrix/components', true, true);
        }
    }

    public function UnInstallFiles()
    {
        DeleteDirFiles(__DIR__.'/admin/', $this->docRoot.'/bitrix/admin');
        if (Dir::isDirectoryExists($this->docRoot . '/bitrix/components/'.$this->partnerId.'/'))
        {
            Dir::deleteDirectory($this->docRoot . '/bitrix/components/'.$this->partnerId.'/');
        }
    }

    public function GetModuleRightList(): array
    {
        return [
            "reference_id" => array("D","R",/*"S",*/"W"),
            "reference" => array(
                "[D] ".Loc::getMessage("FIRSTBIT_APPOINTMENT_DENIED"),
                "[R] ".Loc::getMessage("FIRSTBIT_APPOINTMENT_READ_COMPONENT"),
                //"[S] ".Loc::getMessage("FIRSTBIT_APPOINTMENT_WRITE_SETTINGS"),
                "[W] ".Loc::getMessage("FIRSTBIT_APPOINTMENT_FULL"))
        ];
    }

    private function isSupportD7(): bool
    {
        return CheckVersion(ModuleManager::getVersion("main"), "14.00.00");
    }
}