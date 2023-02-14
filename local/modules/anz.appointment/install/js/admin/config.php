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
	'css' => 'dist/admin.bundle.css',
	'js'  => 'dist/admin.bundle.js',
	'rel' => [
		'color_picker',
		'main.core',
	],
	'skip_core' => false,
    'settings' => [
        'customMainBtnCheckboxId' => Constants::OPTION_KEY_USE_CUSTOM_BTN,
        'customMainBtnInputId'    => Constants::OPTION_KEY_CUSTOM_BTN_ID,
        'startBtnBgColorInput'    => Constants::OPTION_KEY_MAIN_BTN_BG,
        'startBtnTextColorInput'  => Constants::OPTION_KEY_MAIN_BTN_TEXT_CLR,
    ],
];