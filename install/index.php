<?php

use ANZ\Appointment\Agent\Exchange;
use ANZ\Appointment\Core\Installation\Installer;
use ANZ\Appointment\Event\EventManager as ANZEventManager;
use ANZ\Appointment\Config\Configuration;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\IO\Directory as Dir;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

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
                $this->InstallAgents();
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
                    $this->UnInstallAgents();
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
    public function InstallDB(): void
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
    }

    /**
     * @throws \Exception
     */
    public function InstallEvents(): void
    {
        ANZEventManager::registerModuleStartEvent();
    }

    /**
     * @throws \Exception
     */
    public function UnInstallEvents(): void
    {
        ANZEventManager::unregisterModuleStartEvent();
    }

    public function InstallFiles(): void
    {
        CopyDirFiles(__DIR__.'/js/', $this->docRoot.'/bitrix/js/'.$this->partnerId."/".$this->moduleNameShort, true, true);
        CopyDirFiles(__DIR__.'/css/', $this->docRoot.'/bitrix/css/'.$this->partnerId."/".$this->moduleNameShort, true, true);
        CopyDirFiles(__DIR__.'/admin/', $this->docRoot.'/bitrix/admin');
        CopyDirFiles(__DIR__.'/wizards/', $this->docRoot.'/bitrix/wizards', true, true);
        CopyDirFiles(__DIR__.'/components/', $this->docRoot.'/bitrix/components', true, true);
        CopyDirFiles(__DIR__.'/panel/', $this->docRoot.'/bitrix/panel', true, true);
        $logDirectory = Configuration::getInstance()->getLogFileDir(true);
        if (!Dir::isDirectoryExists($logDirectory))
        {
            Dir::createDirectory($logDirectory);
        }

        $htaccessPath = $logDirectory . '/.htaccess';
        if (!is_file($htaccessPath))
        {
            file_put_contents($htaccessPath, 'Deny from all');
        }
    }

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
        if (Dir::isDirectoryExists($this->docRoot . '/bitrix/panel/'.$this->MODULE_ID.'/')){
            Dir::deleteDirectory($this->docRoot . '/bitrix/panel/'.$this->MODULE_ID.'/');
        }

        $srcDir = __DIR__.'/components/'.$this->partnerId.'/';
        $dstDir = $this->docRoot . '/bitrix/components/'.$this->partnerId.'/';
        if (Dir::isDirectoryExists($srcDir))
        {
            if ($dir = opendir($srcDir))
            {
                while ($item = readdir($dir))
                {
                    if (($item != "." && $item != "..") && is_dir($srcDir . $item) && is_dir($dstDir . $item))
                    {
                        try
                        {
                            Dir::deleteDirectory($dstDir . $item);
                        }
                        catch(Exception)
                        {
                            continue;
                        }
                    }
                }
                closedir($dir);
            }
        }
    }

    public function InstallAgents(): void
    {
        foreach ($this->getModuleAgentsData() as $agent)
        {
            $name = $agent['handler'];
            $dbResult = CAgent::GetList(['ID' => 'DESC'], ['NAME' => $name]);
            if ($dbResult && ($existingAgent = $dbResult->Fetch()))
            {
                CAgent::Update($existingAgent['ID'],[
                    'NAME' => $name,
                    'MODULE_ID' => $this->MODULE_ID,
                    'IS_PERIOD' => $agent['period'],
                    'AGENT_INTERVAL' => $agent['interval'],
                    'ACTIVE' => $agent['active'],
                    'NEXT_EXEC' => $agent['nextExec'],
                    'USER_ID' => $agent['userId'],
                    'SORT' => $agent['sort']
                ]);
            }
            else
            {
                CAgent::AddAgent(
                    $name,
                    $this->MODULE_ID,
                    $agent['period'],
                    $agent['interval'],
                    '',
                    $agent['active'],
                    $agent['nextExec'],
                    $agent['sort'],
                    $agent['userId']
                );
            }
        }
    }

    public function UnInstallAgents(): void
    {
        CAgent::RemoveModuleAgents($this->MODULE_ID);
    }

    protected function getModuleAgentsData(): array
    {
        return [
            [
                'handler' => Exchange::class . "::loadData();",
                'period' => "N",
                'interval' => 60,
                'userId' => null,
                'active' => 'Y',
                'nextExec' => date("d.m.Y H:i:s", time() + 60),
                'sort' => 100
            ],
            [
                'handler' => Exchange::class . "::cleanLogFiles();",
                'period' => "N",
                'interval' => 86400,
                'userId' => null,
                'active' => 'Y',
                'nextExec' => date("d.m.Y H:i:s", time() + 60),
                'sort' => 100
            ],
            [
                'handler' => Exchange::class . "::cleanModuleCache();",
                'period' => "N",
                'interval' => 3600,
                'userId' => null,
                'active' => 'Y',
                'nextExec' => date("d.m.Y H:i:s", time() + 60),
                'sort' => 100
            ],
        ];
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
            'main'  => '25.0',
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
