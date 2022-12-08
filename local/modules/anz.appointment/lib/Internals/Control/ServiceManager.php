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
    public function includeModule()
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
        $extensionId = defined('ANZ_APPOINTMENT_JS_EXTENSION') ? ANZ_APPOINTMENT_JS_EXTENSION : Constants::APPOINTMENT_JS_EXTENSION;

        global $APPLICATION;
        $currentUserGroups = (new CUser())->GetUserGroupArray();

        $canSeeForm = ($APPLICATION->GetGroupRight(Constants::APPOINTMENT_MODULE_ID, $currentUserGroups) >= "R");
        $isAdmin = !empty($GLOBALS['USER']) && CurrentUser::get()->isAdmin();

        if ( $canSeeForm || $isAdmin )
        {
            if (!Context::getCurrent()->getRequest()->isAdminSection())
            {
                $optionKey = 'appointment_settings_use_auto_injecting';
                if (Option::get(Constants::APPOINTMENT_MODULE_ID, $optionKey) === "Y")
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
        $arr = explode(DIRECTORY_SEPARATOR, __FILE__);
        $i = array_search("modules",$arr);
        return (string)$arr[$i+1];
    }

    private function __clone(){}
    public function __wakeup(){}
}