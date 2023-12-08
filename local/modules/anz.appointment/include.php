<?php
use ANZ\Appointment\Internals\Control\ServiceManager;
use ANZ\Appointment\Internals\Debug\Logger;

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
    Logger::printToFile(
        date("d.m.Y H:i:s") . '. Error on module including - ' . $e->getMessage(),
    );
}
?>