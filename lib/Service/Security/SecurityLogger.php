<?php

namespace ANZ\Appointment\Service\Security;

use ANZ\Appointment\Config\Configuration;
use Bitrix\Main\Diag\Debug;
use Throwable;

class SecurityLogger
{
    public function log(Throwable $exception, string $context, array $data = []): void
    {
        try
        {
            Debug::writeToFile(
                [
                    'message' => $exception->getMessage(),
                    'class' => get_class($exception),
                    'code' => $exception->getCode(),
                    'context' => $data,
                    'trace' => $exception->getTraceAsString(),
                ],
                $context . ' ' . date('Y-m-d H:i:s'),
                Configuration::getInstance()->getCommonLogFilePath()
            );
        }
        catch (Throwable)
        {
        }
    }
}
