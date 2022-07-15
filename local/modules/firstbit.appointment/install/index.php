<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - install.php
 * 10.07.2022 22:37
 * ==================================================
 */
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\IO\Directory as Dir;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use ANZ\Appointment\Services\Container;
use ANZ\Appointment\Event\EventManager as ANZEventManager;

Loc::loadMessages(__FILE__);

class anz_appointment extends CModule
{
    private CMain $App;
    private ?string $docRoot;
    private string $partnerId;
    private string $moduleNameShort;

    public function __construct(){
        $this->App = $GLOBALS['APPLICATION'];
        $this->docRoot = Application::getDocumentRoot();
        $this->partnerId = 'anz';
        $this->moduleNameShort = 'appointment';

        $arModuleVersion = [];
        include(__DIR__."/version.php");

        $this->MODULE_ID            = $this->partnerId.".".$this->moduleNameShort;
        $this->MODULE_VERSION       = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE  = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME          = Loc::getMessage("ANZANZANZANZANZANZANZANZANZANZANZANZANZANZANZ_APPOINTMENT_MODULE_NAME");
        $this->MODULE_DESCRIPTION   = Loc::getMessage("ANZANZANZANZANZANZANZANZANZANZANZANZANZANZANZ_APPOINTMENT_MODULE_DESCRIPTION");
        $this->PARTNER_NAME         = Loc::getMessage("ANZANZANZANZANZANZANZANZANZANZANZANZANZANZANZ_APPOINTMENT_PARTNER_NAME");
        $this->PARTNER_URI          = Loc::getMessage("ANZ_APPOINTMENT_PARTNER_URI");
        $this->MODULE_SORT          = 100;
        $this->MODULE_GROUP_RIGHTS  = "Y";
        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = "Y";
    }

    public function DoInstall(): void
    {
        try
        {
            $this->checkRequirements();

            ModuleManager::registerModule($this->MODULE_ID);
            Loader::includeModule($this->MODULE_ID);

            $this->InstallDB();
            $this->InstallEvents();
            $this->InstallFiles();

            $this->App->IncludeAdminFile(
                Loc::getMessage("ANZ_APPOINTMENT_INSTALL_TITLE"),
                __DIR__."/step.php"
            );
        }
        catch (Exception $e)
        {
            $this->App->ThrowException($e->getMessage());
        }
    }

    public function DoUninstall(): void
    {
        try {
            $request = Context::getCurrent()->getRequest();

            if ($request->get('step') < 2)
            {
                $this->App->IncludeAdminFile(
                    Loc::getMessage("ANZ_APPOINTMENT_UNINSTALL_TITLE"),
                    __DIR__."/unStep_1.php"
                );
            }
            else
            {
                Loader::includeModule($this->MODULE_ID);

                $this->UnInstallFiles();
                $this->UnInstallEvents();
                if ($request->get('saveData') !== "Y"){
                    $this->UnInstallDB();
                }
                Option::delete($this->MODULE_ID);
                ModuleManager::unRegisterModule($this->MODULE_ID);

                $this->App->IncludeAdminFile(
                    Loc::getMessage("ANZ_APPOINTMENT_UNINSTALL_TITLE"),
                    __DIR__."/unStep_2.php"
                );
            }
        }
        catch (Exception $e)
        {
            $this->App->ThrowException($e->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function InstallDB()
    {
        try {
            Loader::includeModule($this->MODULE_ID);

            $recordDataClass = Container::getInstance()->getRecordDataClass();
            $connection = Application::getConnection();
            $recordTableName = Base::getInstance($recordDataClass)->getDBTableName();
            if(!$connection->isTableExists($recordTableName))
            {
                Base::getInstance($recordDataClass)->createDbTable();
            }
        }
        catch (Exception $e){
            throw new Exception(Loc::getMessage("ANZ_APPOINTMENT_INSTALL_ERROR")." - ". $e->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function UnInstallDB(): void
    {
        try {
            Loader::includeModule($this->MODULE_ID);

            $recordDataClass = Container::getInstance()->getRecordDataClass();
            $connection = Application::getConnection();
            $recordTableName = Base::getInstance($recordDataClass)->getDBTableName();
            if($connection->isTableExists($recordTableName))
            {
                $connection->dropTable($recordTableName);
            }
        }
        catch(Exception $e){
            throw new Exception(Loc::getMessage("ANZ_APPOINTMENT_UNINSTALL_ERROR")." - ". $e->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function InstallEvents()
    {
        ANZEventManager::createMessageEvents();
        ANZEventManager::addEventHandlers();
    }

    /**
     * @throws \Exception
     */
    public function UnInstallEvents()
    {
        ANZEventManager::deleteMessageEvents();
        ANZEventManager::removeEventHandlers();
    }

    public function InstallFiles()
    {
        CopyDirFiles(__DIR__.'/js/', $this->docRoot.'/bitrix/js/'.$this->partnerId."/".$this->moduleNameShort, true, true);
        CopyDirFiles(__DIR__.'/css/', $this->docRoot.'/bitrix/css/'.$this->partnerId."/".$this->moduleNameShort, true, true);
        CopyDirFiles(__DIR__.'/admin/', $this->docRoot.'/bitrix/admin', true);
        CopyDirFiles(__DIR__.'/components/', $this->docRoot.'/bitrix/components', true, true);
    }

    public function UnInstallFiles()
    {
        DeleteDirFiles(__DIR__.'/admin/', $this->docRoot.'/bitrix/admin');

        if (Dir::isDirectoryExists($this->docRoot . '/bitrix/css/'.$this->partnerId."/".$this->moduleNameShort.'/')){
            Dir::deleteDirectory($this->docRoot . '/bitrix/css/'.$this->partnerId. "/".$this->moduleNameShort.'/');
        }
        if (Dir::isDirectoryExists($this->docRoot . '/bitrix/js/'.$this->partnerId."/".$this->moduleNameShort.'/')){
            Dir::deleteDirectory($this->docRoot . '/bitrix/js/'.$this->partnerId."/".$this->moduleNameShort.'/');
        }

        if (Dir::isDirectoryExists($path = $this->docRoot . '/bitrix/components/'.$this->partnerId.'/')) {
            if ($dir = opendir($path)) {
                while ($item = readdir($dir))
                {
                    if (strpos($item, $this->moduleNameShort.".") === 0)
                    {
                        if (is_dir($path . $item))
                        {
                            try {
                                Dir::deleteDirectory($path . $item);
                            }catch(Exception $e){
                                continue;
                            }
                        }
                    }
                }
                closedir($dir);
            }
        }
    }

    public function GetModuleRightList(): array
    {
        return [
            "reference_id" => array("D","R","W"),
            "reference" => array(
                "[D] ".Loc::getMessage("ANZ_APPOINTMENT_DENIED"),
                "[R] ".Loc::getMessage("ANZ_APPOINTMENT_READ_COMPONENT"),
                "[W] ".Loc::getMessage("ANZ_APPOINTMENT_FULL"))
        ];
    }

    /**
     * @throws \Exception
     */
    protected function checkRequirements(): void
    {
        $requirePhp = '7.4.0';

        if (!CheckVersion(PHP_VERSION, $requirePhp))
        {
            throw new Exception(Loc::getMessage(
                'ANZ_APPOINTMENT_INSTALL_REQUIRE_PHP',
                [ '#VERSION#' => $requirePhp ]
            ));
        }

        $requireModules = [
            'main'  => '22.0.0',
        ];

        foreach ($requireModules as $moduleName => $moduleVersion)
        {
            $currentVersion = ModuleManager::getVersion($moduleName);

            if (!CheckVersion($currentVersion, $moduleVersion))
            {
                throw new Exception(Loc::getMessage('ANZ_APPOINTMENT_INSTALL_ERROR_VERSION', [
                    '#MODULE#' => $moduleName,
                    '#VERSION#' => $moduleVersion
                ]));
            }
        }
    }
}