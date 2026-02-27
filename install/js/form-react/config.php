<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
    die();
}
use ANZ\Appointment\Config\Configuration;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;

$css = '';
$js = '';
try
{
    $root = Application::getDocumentRoot();
    $pathToJs = '/js/anz/appointment/form-react/build/static/js/';
    $pathToCss = '/js/anz/appointment/form-react/build/static/css/';
    $useLocal = Configuration::isInLocalHolder();
    if ($useLocal)
    {
        $jsPattern = $root . '/' . Loader::LOCAL_HOLDER . $pathToJs . 'main.*.js';
        $cssPattern = $root . '/' . Loader::LOCAL_HOLDER . $pathToCss . 'main.*.css';
    }
    else
    {
        $jsPattern = $root . '/' . Loader::BITRIX_HOLDER . $pathToJs . 'main.*.js';
        $cssPattern = $root . '/' . Loader::BITRIX_HOLDER . $pathToCss . 'main.*.css';
    }

    $jsFiles = glob($jsPattern);
    if (is_array($jsFiles) && count($jsFiles) > 0)
    {
        $js = 'build/static/js/'.basename(current($jsFiles));
    }

    $cssFiles = glob($cssPattern);
    if (is_array($cssFiles) && count($cssFiles) > 0)
    {
        $css = 'build/static/css/'.basename(current($cssFiles));
    }
}
catch (Throwable)
{
}

return [
	'css' => $css,
	'js' => $js,
	'rel' => ['date', 'masked_input'],
	'skip_core' => false,
];