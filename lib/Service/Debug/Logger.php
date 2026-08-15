<?php

namespace ANZ\Appointment\Service\Debug;

use ANZ\Appointment\Config\Configuration;
use Bitrix\Main\Application;
use Bitrix\Main\Diag\Debug;
use Throwable;

class Logger
{
    public function log(Throwable $exception, string $context, ?string $logFilePath = null): void
    {
        try
        {
            if (!$logFilePath)
            {
                $logFilePath = Configuration::getInstance()->getCommonLogFilePath();
            }
            Debug::writeToFile(
                [
                    'message' => str_replace(Application::getDocumentRoot(), '[DOC_ROOT]', $exception->getMessage()),
                    'class' => get_class($exception),
                    'code' => $exception->getCode()
                ],
                $context,
                $logFilePath
            );
        }
        catch (Throwable)
        {
        }
    }
}
