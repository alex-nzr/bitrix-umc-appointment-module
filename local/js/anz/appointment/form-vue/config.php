<?php

use ANZ\Appointment\Config\Configuration;
use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

return [
	'css' => 'dist/app.bundle.css',
	'js' => 'dist/app.bundle.js',
	'rel' => [
		'ui.vue3',
		'ui.vue3.pinia',
		'main.core',
	],
	'skip_core' => false,
    'lang' => ['lang/ru/loc.php'],
    'settings' => [
        'useCustomBtn' => Configuration::getInstance()->isCustomBtnEnabled(),
        'customBtnId' => Configuration::getInstance()->getCustomBtnAttrId(),
        'name' => Loc::getMessage('ANZ_JS_VUE_APP_NAME'),
    ]
];