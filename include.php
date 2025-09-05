<?php

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Core\ServiceManager;
use Bitrix\Main\Diag\Debug;

try
{
    if (is_file(__DIR__ . '/vendor/autoload.php'))
    {
        require_once __DIR__ . '/vendor/autoload.php';
    }
    ServiceManager::getInstance()->includeModule();
}
catch (Throwable $e)
{
    Debug::writeToFile(
        'Error on module including - ' . $e->getMessage(),
        date("d.m.Y H:i:s"),
        Configuration::getInstance()->getCommonLogFilePath()
    );
}
?>