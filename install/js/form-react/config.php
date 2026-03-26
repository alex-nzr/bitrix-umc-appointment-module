<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
    die();
}
use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Config\Constants;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;

$assets = [];
$settings = [];
try
{
    $assets = resolveExtensionAssets();

    $settings = [
        'mainColor' => Configuration::getInstance()->getTemplateColors()[Constants::OPTION_KEY_TEMPLATE_MAIN_COLOR],
        'privacyPolicyUrl' => Configuration::getInstance()->getPrivacyPageLink(),
        'schedulePeriodDays' => Configuration::getInstance()->getExchangeSchedulePeriod(),
        'logoImageSrc' => Configuration::getInstance()->getLogoFilePath(),
        'defaultClinicUid' => Configuration::getInstance()->getDefaultClinic(),
        'servicesEnabled' => Configuration::getInstance()->isServicesEnabled(),
        'emailNotificationEnabled' => Configuration::getInstance()->isEmailNotificationEnabled(),
        'confirmationType' => Configuration::getInstance()->getExchangeConfirmMode(),
        'useCustomButton' => Configuration::getInstance()->isCustomBtnEnabled(),
        'customButtonSelector' => Configuration::getInstance()->getCustomBtnSelector(),
        'phoneInputMask' => "+7(000)000-00-00",
    ];
}
catch (Throwable $e)
{
    $settings = ['error' => $e->getMessage()];
}

return $assets + [
    'rel' => ['main.core'],
    'skip_core' => false,
    'settings' => $settings,
    'lang' => ['lang/ru/lang.php'],
];

/**
 * @throws \ANZ\Appointment\Core\Exception\ConfigurationException
 */
function resolveExtensionAssets(): array
{
    $css = '';
    $js = '';
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

    return [
        'js' => $js,
        'css' => $css,
    ];
}
