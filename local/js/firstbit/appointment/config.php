<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

return [
	'css' => 'dist/appointment.bundle.css',
	'js' => 'dist/appointment.bundle.js',
	'rel' => [
		'main.core',
		'normalize.css',
	],
	'skip_core' => false,
];