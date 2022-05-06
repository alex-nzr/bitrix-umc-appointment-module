<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

return [
	'css' => 'dist/bx_popup.bundle.css',
	'js'  => 'dist/bx_popup.bundle.js',
	'rel' => [
		'main.polyfill.core',
	],
	'skip_core' => true,
];