<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.07.2022
 * ==================================================
*/
namespace ANZ\Appointment\Event;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Event\Handler\Main;
use ANZ\Appointment\Internals\ServiceManager;
use Bitrix\Main\EventManager as BitrixEventManager;

class EventManager
{
    /**
     * @throws \Exception
     */
    public static function registerModuleStartEvent(): void
    {
        BitrixEventManager::getInstance()->registerEventHandler(
            'main',
            'OnPageStart',
            Configuration::getModuleId(),
            static::class,
            'addRuntimeEventHandlers'
        );
    }

    /**
     * @throws \Exception
     */
    public static function unregisterModuleStartEvent(): void
    {
        BitrixEventManager::getInstance()->unRegisterEventHandler(
            'main',
            'OnPageStart',
            Configuration::getModuleId(),
            static::class,
            'addRuntimeEventHandlers'
        );
    }

    public static function addRuntimeEventHandlers(): void
    {
        foreach (static::getRunTimeEventHandlers() as $moduleId => $eventData)
        {
            foreach ($eventData as $eventName => $handlers)
            {
                foreach ($handlers as $handlerData)
                {
                    if (is_array($handlerData) && is_callable($handlerData['handler']))
                    {
                        BitrixEventManager::getInstance()->addEventHandler(
                            $moduleId,
                            $eventName,
                            $handlerData['handler'],
                            false,
                            key_exists('sort', $handlerData) ? $handlerData['sort'] : 100
                        );
                    }
                }
            }
        }
    }

    protected static function getRunTimeEventHandlers(): array
    {
        return [
            'main' => [
                'OnBuildGlobalMenu' => [
                    [
                        'handler' => [Main::class, 'onBuildGlobalMenu'],
                        'sort'   => 100
                    ],
                ],
                'OnProlog' => [
                    [
                        'handler' => [ServiceManager::class, 'includeAppointmentExtension'],
                        'sort'   => 100
                    ],
                ],
            ]
        ];
    }
}