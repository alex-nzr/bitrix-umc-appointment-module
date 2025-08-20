<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - config.php
 * 10.07.2022 22:37
 * ==================================================
 */

use ANZ\Appointment\Config\Constants;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

return [
	'css' => 'dist/index.bundle.css',
	'js'  => 'dist/index.bundle.js',
	'rel' => [
		'ui.buttons',
		'main.core',
		'main.popup',
	],
	'skip_core' => false,
    'settings' => [
        'ignoreFtpOptionKey' => Constants::REQUEST_KEY_IGNORE_FTP
    ],
    'lang' => ['lang/ru/js_lang.php'],
];