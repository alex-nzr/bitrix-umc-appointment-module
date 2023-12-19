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


use ANZ\Appointment\Internals\Installation\Installer;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\IO\Directory as Dir;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use ANZ\Appointment\Internals\Control\EventManager as ANZEventManager;

Loc::loadMessages(__FILE__);

/**
 * @class anz_appointment
 */
class anz_appointment extends CModule
{
    public $MODULE_ID = 'anz.appointment';

    private CMain $App;
    private ?string $docRoot;
    private string $partnerId;
    private string $moduleNameShort;

    public function __construct()
    {
        $this->App = $GLOBALS['APPLICATION'];
        $this->docRoot = Application::getDocumentRoot();
        $this->partnerId = 'anz';
        $this->moduleNameShort = 'appointment';

        $arModuleVersion = [];
        include(__DIR__."/version.php");

        $this->MODULE_ID            = $this->partnerId.".".$this->moduleNameShort;
        $this->MODULE_VERSION       = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE  = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME          = Loc::getMessage("ANZ_APPOINTMENT_MODULE_NAME");
        $this->MODULE_DESCRIPTION   = Loc::getMessage("ANZ_APPOINTMENT_MODULE_DESCRIPTION");
        $this->PARTNER_NAME         = Loc::getMessage("ANZ_APPOINTMENT_PARTNER_NAME");
        $this->PARTNER_URI          = Loc::getMessage("ANZ_APPOINTMENT_PARTNER_URI");
        $this->MODULE_SORT          = 100;
        $this->MODULE_GROUP_RIGHTS  = "Y";
        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = "Y";
    }

    /**
     * @return bool
     */
    public function DoInstall(): bool
    {
        $result = true;
        try
        {
            $this->checkRequirements();
            ModuleManager::registerModule($this->MODULE_ID);

            /*if (Loader::includeSharewareModule($this->MODULE_ID) === Loader::MODULE_DEMO_EXPIRED)
            {
                ModuleManager::unRegisterModule($this->MODULE_ID);
                throw new Exception("Demo mode expired. Installation aborted.");
            }*/

            if (Loader::includeModule($this->MODULE_ID))
            {
                $this->InstallDB();
                $this->InstallEvents();
                $this->InstallFiles();

                $this->App->IncludeAdminFile(
                    Loc::getMessage("ANZ_APPOINTMENT_INSTALL_TITLE"),
                    __DIR__."/step.php"
                );
            }
            else
            {
                throw new Exception(Loc::getMessage('ANZ_APPOINTMENT_INSTALL_ERROR') . " Module not registered");
            }
        }
        catch (Exception $e)
        {
            $result = false;
            $this->App->ThrowException($e->getMessage());
            ModuleManager::unRegisterModule($this->MODULE_ID);
        }

        return $result;
    }

    public function DoUninstall(): bool
    {
        $result = true;
        try {
            /*if (Loader::includeSharewareModule($this->MODULE_ID) === Loader::MODULE_DEMO_EXPIRED)
            {
                $this->UnInstallFiles();
                ModuleManager::unRegisterModule($this->MODULE_ID);
                return true;
            }*/

            if (Loader::includeModule($this->MODULE_ID))
            {
                $request = Context::getCurrent()->getRequest();

                if ((int)$request->get('step') < 2)
                {
                    $this->App->IncludeAdminFile(
                        Loc::getMessage("ANZ_APPOINTMENT_UNINSTALL_TITLE"),
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
                        Loc::getMessage("ANZ_APPOINTMENT_UNINSTALL_TITLE"),
                        __DIR__."/unStep_2.php"
                    );
                }
            }
            else
            {
                throw new Exception(Loc::getMessage('ANZ_APPOINTMENT_UNINSTALL_ERROR') . " Module not registered");
            }
        }
        catch (Exception $e)
        {
            $result = false;
            $this->App->ThrowException($e->getMessage());
        }

        return $result;
    }

    /**
     * @throws \Exception
     */
    public function InstallDB()
    {
        $res = Installer::installModule();
        if (!$res->isSuccess())
        {
            throw new Exception(
                Loc::getMessage("ANZ_APPOINTMENT_INSTALL_ERROR")
                ." - ". implode('; ', $res->getErrorMessages())
            );
        }
    }

    /**
     * @throws \Exception
     */
    public function UnInstallDB(): void
    {
        $res = Installer::uninstallModule();
        if (!$res->isSuccess())
        {
            throw new Exception(
                Loc::getMessage("ANZ_APPOINTMENT_UNINSTALL_ERROR")
                ." - ". implode('; ', $res->getErrorMessages())
            );
        }

        Option::delete($this->MODULE_ID);
    }

    /**
     * @throws \Exception
     */
    public function InstallEvents()
    {
        ANZEventManager::addBasicEventHandlers();
    }

    /**
     * @throws \Exception
     */
    public function UnInstallEvents()
    {
        ANZEventManager::removeBasicEventHandlers();
    }

    /**
     * @return void
     */
    public function InstallFiles(): void
    {
        CopyDirFiles(__DIR__.'/js/', $this->docRoot.'/bitrix/js/'.$this->partnerId."/".$this->moduleNameShort, true, true);
        CopyDirFiles(__DIR__.'/css/', $this->docRoot.'/bitrix/css/'.$this->partnerId."/".$this->moduleNameShort, true, true);
        CopyDirFiles(__DIR__.'/admin/', $this->docRoot.'/bitrix/admin');
        CopyDirFiles(__DIR__.'/wizards/', $this->docRoot.'/bitrix/wizards', true, true);
        CopyDirFiles(__DIR__.'/components/', $this->docRoot.'/bitrix/components', true, true);
    }

    /**
     * @return void
     */
    public function UnInstallFiles(): void
    {
        DeleteDirFiles(__DIR__.'/admin/', $this->docRoot.'/bitrix/admin');

        if (Dir::isDirectoryExists($this->docRoot . '/bitrix/css/'.$this->partnerId."/".$this->moduleNameShort.'/')){
            Dir::deleteDirectory($this->docRoot . '/bitrix/css/'.$this->partnerId. "/".$this->moduleNameShort.'/');
        }
        if (Dir::isDirectoryExists($this->docRoot . '/bitrix/js/'.$this->partnerId."/".$this->moduleNameShort.'/')){
            Dir::deleteDirectory($this->docRoot . '/bitrix/js/'.$this->partnerId."/".$this->moduleNameShort.'/');
        }
        if (Dir::isDirectoryExists($this->docRoot . '/bitrix/wizards/'.$this->partnerId."/".$this->moduleNameShort.'/')){
            Dir::deleteDirectory($this->docRoot . '/bitrix/wizards/'.$this->partnerId."/".$this->moduleNameShort.'/');
        }

        if (Dir::isDirectoryExists($path = $this->docRoot . '/bitrix/components/'.$this->partnerId.'/')) {
            if ($dir = opendir($path)) {
                while ($item = readdir($dir))
                {
                    if (str_starts_with($item, $this->moduleNameShort . "."))
                    {
                        if (is_dir($path . $item))
                        {
                            try {
                                Dir::deleteDirectory($path . $item);
                            }catch(Exception){
                                continue;
                            }
                        }
                    }
                }
                closedir($dir);
            }
        }
    }

    /**
     * @return array[]
     */
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
        $requirePhp = '8.1.0';

        if (!version_compare(PHP_VERSION, $requirePhp, '>='))
        {
            throw new Exception(Loc::getMessage(
                'ANZ_APPOINTMENT_INSTALL_REQUIRE_PHP',
                [ '#VERSION#' => $requirePhp ]
            ));
        }

        $requireModules = [
            'main'  => '23.675.0',
        ];

        foreach ($requireModules as $moduleName => $moduleVersion)
        {
            $currentVersion = ModuleManager::getVersion($moduleName);

            if (!version_compare($currentVersion, $moduleVersion, '>='))
            {
                throw new Exception(Loc::getMessage('ANZ_APPOINTMENT_INSTALL_ERROR_VERSION', [
                    '#MODULE#' => $moduleName,
                    '#VERSION#' => $moduleVersion
                ]));
            }
        }
    }
}