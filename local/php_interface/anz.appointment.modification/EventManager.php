<?php
namespace Firstbit\Stoma;

use Bitrix\Main\EventManager as BitrixEventManager;
use ANZ\Appointment\Config\Constants;
use ANZ\Appointment\Event\EventType;

/**
 * Class EventManager
 * @package Firstbit\Stoma
 */
class EventManager
{
    protected static array $events = [
        [
            'module'    => Constants::APPOINTMENT_MODULE_ID,
            'eventType' => EventType::ON_BEFORE_CLINICS_PARSED,
            'class'     => CustomXmlParser::class,
            'method'    => 'onBeforeClinicsParsed',
        ],
        [
            'module'    => Constants::APPOINTMENT_MODULE_ID,
            'eventType' => EventType::ON_AFTER_CLINICS_PARSED,
            'class'     => CustomXmlParser::class,
            'method'    => 'onAfterClinicsParsed',
        ],
        [
            'module'    => Constants::APPOINTMENT_MODULE_ID,
            'eventType' => EventType::ON_BEFORE_EMPLOYEES_PARSED,
            'class'     => CustomXmlParser::class,
            'method'    => 'onBeforeEmployeesParsed',
        ],
        [
            'module'    => Constants::APPOINTMENT_MODULE_ID,
            'eventType' => EventType::ON_AFTER_EMPLOYEES_PARSED,
            'class'     => CustomXmlParser::class,
            'method'    => 'onAfterEmployeesParsed',
        ],
        [
            'module'    => Constants::APPOINTMENT_MODULE_ID,
            'eventType' => EventType::ON_BEFORE_SCHEDULE_PARSED,
            'class'     => CustomXmlParser::class,
            'method'    => 'onBeforeScheduleParsed',
        ],
        [
            'module'    => Constants::APPOINTMENT_MODULE_ID,
            'eventType' => EventType::ON_AFTER_SCHEDULE_PARSED,
            'class'     => CustomXmlParser::class,
            'method'    => 'onAfterScheduleParsed',
        ],
    ];

    /**
     * @return void
     */
    public static function addEventHandlers(): void
    {
        foreach (static::$events as $event)
        {
            BitrixEventManager::getInstance()->addEventHandler(
                $event['module'],
                $event['eventType'],
                [
                    $event['class'],
                    $event['method']
                ],
            );
        }
    }
}