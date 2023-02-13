<?php

use Bitrix\Main\Loader;
use Firstbit\Stoma\EventManager as FirstBitEventManager;

$arClasses = [
    'FirstBit\\Stoma\\CustomXmlParser' => "/local/php_interface/anz.appointment.modification/CustomXmlParser.php",
    'FirstBit\\Stoma\\EventManager'    => "/local/php_interface/anz.appointment.modification/EventManager.php",
];

try
{
    if (Loader::includeModule('anz.appointment'))
    {
        define('ANZ_APPOINTMENT_JS_EXTENSION', 'firstbit.appointment.custom_popup');
        Loader::registerAutoLoadClasses(null, $arClasses);
        FirstBitEventManager::addEventHandlers();
    }
}
catch (Exception $e)
{
    //log error
}