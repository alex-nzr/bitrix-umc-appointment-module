<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

return [
	'css' => 'dist/admin.bundle.css',
	'js'  => 'dist/admin.bundle.js',
	'rel' => [
		'main.polyfill.core',
		'color_picker',
	],
	'skip_core' => true,
];