<?php

namespace ANZ\Appointment\Service\Security;

use ANZ\Appointment\Config\Configuration;
use Bitrix\Main\Application;
use Bitrix\Main\Diag\Debug;
use CEventLog;
use Throwable;

class SecurityLogger
{
    public function log(Throwable $exception, string $context, array $data = []): void
    {
        try
        {
            CEventLog::Add([
                'SEVERITY' => CEventLog::SEVERITY_ERROR,
                'AUDIT_TYPE_ID' => 'ANZ_APPOINTMENT_SECURITY',
                'MODULE_ID' => Configuration::getModuleId(),
                'DESCRIPTION' => json_encode([
                    'message' => str_replace(Application::getDocumentRoot(), '[DOC_ROOT]', $exception->getMessage()),
                    'class' => get_class($exception),
                    'code' => $exception->getCode(),
                    'context' => $context,
                    'data' => $data
                ], JSON_UNESCAPED_UNICODE),
            ]);
        }
        catch (Throwable)
        {
        }
    }
}
