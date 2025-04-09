<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 21.11.2022
 * ==================================================
*/
namespace ANZ\Appointment\Internals;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Config\Constants;
use ANZ\Appointment\Config\UrlRewriter;
use ANZ\Appointment\Controller\MessageController;
use ANZ\Appointment\Controller\OneCController;
use ANZ\Appointment\Service\Container;
use ANZ\Appointment\Service\Localization;
use ANZ\BitUmc\SDK\Core\Trait\Singleton;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\UI\Extension;
use Exception;

/**
 * @method static ServiceManager getInstance()
 */
class ServiceManager
{
    use Singleton;

    protected static ?string $moduleId = null;
    protected static ?string $moduleParentDirectoryName = null;

    /**
     * @throws \Exception
     */
    public function includeModule(): void
    {
        Localization::loadMessages();
        $this->includeControllers();
        $this->updateUrlRewriter();
        $this->includeDependentModules();
        $this->includeDependentExtensions();
    }

    /**
     * @throws \Exception
     */
    private function includeControllers(): void
    {
        $arControllers = [
            OneCController::class  => 'lib/Controller/OneCController.php',
            MessageController::class => 'lib/Controller/MessageController.php',
        ];

        Loader::registerAutoLoadClasses(static::getModuleId(), $arControllers);
    }

    /**
     * @throws \Exception
     */
    public function includeDependentModules(): void
    {
        $dependencies = [
            'main', 'iblock'
        ];

        foreach ($dependencies as $dependency) {
            if (!Loader::includeModule($dependency)){
                throw new Exception("Can not include module '$dependency'");
            }
        }
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function includeDependentExtensions(): void
    {
        Extension::load(["ui.icons",]);
        if (Context::getCurrent()->getRequest()->isAdminSection())
        {
            Extension::load([
                static::getModuleId().'.admin', static::getModuleId().'.ftp-map',
            ]);
        }
    }

    /**
     * @throws \Exception
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function includeAppointmentExtension(): void
    {
        $isAdminSection = Context::getCurrent()->getRequest()->isAdminSection();
        if (Configuration::getInstance()->isAutoIncludingOn() && !$isAdminSection && !ServiceManager::isModuleInstallingNow())
        {
            if ( Container::getInstance()->getUserPermissions()->checkReadPermissions()
                || Container::getInstance()->getUserPermissions()->isAdmin()
            )
            {
                Extension::load(
                    defined('ANZ_APPOINTMENT_JS_EXTENSION')
                        ? constant('ANZ_APPOINTMENT_JS_EXTENSION')
                        : Constants::APPOINTMENT_JS_EXTENSION
                );
            }
        }
    }

    public static function getModuleId(): string
    {
        return Configuration::getInstance()->getModuleId();
    }

    public static function getModuleParentDirectoryName(): ?string
    {
        if (empty(static::$moduleParentDirectoryName))
        {
            $arr = explode(DIRECTORY_SEPARATOR, __FILE__);
            $i = array_search("modules", $arr);
            static::$moduleParentDirectoryName = $arr[$i - 1];
        }
        return static::$moduleParentDirectoryName;
    }

    public static function isModuleInstallingNow(): bool
    {
        $request = Context::getCurrent()->getRequest();
        return $request->get('id') === static::getModuleId()
            && ($request->get('install') === 'Y' || $request->get('uninstall') === 'Y');
    }

    /**
     * @throws \Exception
     */
    protected function updateUrlRewriter(): void
    {
        if (Configuration::getInstance()->needToUpdateUrlRewriteConditions())
        {
            UrlRewriter::updateRules();
        }
    }
}