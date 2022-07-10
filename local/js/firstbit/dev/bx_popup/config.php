<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - Config.php
 * 10.07.2022 22:37
 * ==================================================
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

return [
	'css' => 'dist/bx_popup.bundle.css',
	'js'  => 'dist/bx_popup.bundle.js',
	'rel' => [
		'date',
		'main.core',
		'ui.dialogs.messagebox',
	],
	'skip_core' => false,
    'lang' => ['lang/ru/js_lang_phrases.php'],
];