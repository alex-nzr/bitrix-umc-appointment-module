<?php

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;

$moduleId = 'anz.appointment';

try
{
    $arControllers = [
        '\\ANZ\\Appointment\\Controllers\\OneCController'  => 'lib/Controllers/OneCController.php',
        '\\ANZ\\Appointment\\Controllers\\MessageController' => 'lib/Controllers/MessageController.php',
    ];
    Loader::registerAutoLoadClasses($moduleId, $arControllers);
}
catch(Exception $e)
{
    Debug::writeToFile(date('d.m.Y H:i:s') . " " . $e->getMessage(), 'Error in include.php');
}
?>