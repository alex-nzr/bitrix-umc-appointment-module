<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.07.2022
 * ==================================================
*/
namespace ANZ\Appointment\Event;

use ANZ\Appointment\Internals\ServiceManager;
use Bitrix\Main\EventResult;
use Exception;

/**
 * Class Event
 * @package ANZ\Appointment\Event
 */
class Event extends \Bitrix\Main\Event
{
    /**
     * @param string $eventName
     * @param $params
     * @return array|null
     * @throws \Exception
     */
    public static function getEventHandlersResult(string $eventName, $params): ?array
    {
        return static::sendEvent($eventName, $params);
    }

    /**
     * @param string $eventName
     * @param $params
     * @return array|null
     * @throws \Exception
     */
    protected static function sendEvent(string $eventName, $params): ?array
    {
        $event = new static(
            ServiceManager::getModuleId(),
            $eventName,
            $params
        );
        $event->send();

        return static::processEventResult($event);
    }

    /**
     * @throws \Exception
     */
    protected static function processEventResult(Event $event): ?array
    {
        $result = $event->getParameters();
        foreach ($event->getResults() as $eventResult)
        {
            switch($eventResult->getType())
            {
                case EventResult::ERROR:
                    throw new Exception(json_encode($event->getParameters()));
                case EventResult::SUCCESS:
                    $handlerResult = $eventResult->getParameters();
                    if (is_array($handlerResult)){
                        $result = array_merge($result, $handlerResult);
                    }
                    break;
                case EventResult::UNDEFINED:
                    // handle unexpected unknown result
                    break;
            }
        }
        return $result;
    }
}