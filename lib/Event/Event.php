<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.07.2022
 * ==================================================
*/
namespace ANZ\Appointment\Event;

use ANZ\Appointment\Config\Configuration;
use Bitrix\Main\EventResult;
use Exception;

class Event extends \Bitrix\Main\Event
{
    /**
     * @throws \Exception
     */
    public static function getEventHandlersResult(string $eventName, $params): ?array
    {
        return static::sendEvent($eventName, $params);
    }

    /**
     * @throws \Exception
     */
    protected static function sendEvent(string $eventName, $params): ?array
    {
        $event = new static(
            Configuration::getModuleId(),
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
                    if (is_array($handlerResult))
                    {
                        if (array_is_list($result) || array_is_list($handlerResult))
                        {
                            $result = $handlerResult;
                        }
                        else
                        {
                            $result = array_replace_recursive($result, $handlerResult);
                        }
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
