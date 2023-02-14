<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - ServiceManager.php
 * 21.11.2022 12:11
 * ==================================================
 */
namespace ANZ\Appointment\Internals\Control;

use ANZ\Appointment\Config\Constants;
use ANZ\Appointment\Controller\MessageController;
use ANZ\Appointment\Controller\OneCController;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Bitrix\Main\UI\Extension;
use CUser;
use Exception;

/**
 * Class ServiceManager
 * @package ANZ\Appointment\Internals\Control
 */
class ServiceManager
{
    protected static ?ServiceManager $instance = null;
    protected static ?string $moduleId = null;

    private function __construct(){}

    /**
     * @return \ANZ\Appointment\Internals\Control\ServiceManager
     */
    public static function getInstance(): ServiceManager
    {
        if (static::$instance === null)
        {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @throws \Exception
     */
    public function includeModule(): void
    {
        $this->includeControllers();
        $this->includeDependentModules();
        $this->includeDependentExtensions();
    }

    /**
     * @throws \Bitrix\Main\LoaderException
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
     * @throws \Bitrix\Main\LoaderException
     */
    public function includeDependentExtensions(): void
    {
        Extension::load([
            static::getModuleId().'.admin', "ui.icons",
        ]);
    }

    /**
     * @throws \Exception
     */
    public static function includeAppointmentExtension(): void
    {
        $extensionId = defined('ANZ_APPOINTMENT_JS_EXTENSION') ? constant('ANZ_APPOINTMENT_JS_EXTENSION') : Constants::APPOINTMENT_JS_EXTENSION;

        global $APPLICATION;
        $currentUserGroups = (new CUser())->GetUserGroupArray();

        $canSeeForm = ($APPLICATION->GetGroupRight(static::getModuleId(), $currentUserGroups) >= "R");
        $isAdmin = !empty($GLOBALS['USER']) && CurrentUser::get()->isAdmin();

        if ( $canSeeForm || $isAdmin )
        {
            if (!Context::getCurrent()->getRequest()->isAdminSection())
            {
                $optionKey = Constants::OPTION_KEY_AUTO_INC;
                if (Option::get(static::getModuleId(), $optionKey) === "Y")
                {
                    Extension::load($extensionId);
                }
            }
        }
    }

    /**
     * @return string
     */
    public static function getModuleId(): string
    {
        if (static::$moduleId === null)
        {
            $arr = explode(DIRECTORY_SEPARATOR, __FILE__);
            $i = array_search("modules", $arr);
            static::$moduleId = $arr[$i+1];
        }
        return (string)static::$moduleId;
    }

    private function __clone(){}
    public function __wakeup(){}
}