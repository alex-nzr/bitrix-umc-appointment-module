<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 21.11.2022
 * ==================================================
*/
namespace ANZ\Appointment\Internals;

use ANZ\Appointment\Config\Configuration;
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

    /**
     * @throws \Exception
     */
    public function includeModule(): void
    {
        Localization::loadMessages();
        $this->includeControllers();
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
        Extension::load(['ui.icons', 'ui.buttons', 'ui.hint']);
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
                Extension::load(Configuration::getInstance()->getJsExtensionName());
            }
        }
    }

    /**
     * @throws \Exception
     */
    public static function getModuleId(): string
    {
        return Configuration::getModuleId();
    }

    public static function isModuleInstallingNow(): bool
    {
        $request = Context::getCurrent()->getRequest();
        return $request->get('id') === static::getModuleId()
            && ($request->get('install') === 'Y' || $request->get('uninstall') === 'Y');
    }
}