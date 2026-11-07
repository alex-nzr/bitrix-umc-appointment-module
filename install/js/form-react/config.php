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
        'captchaEnabled' => Configuration::getInstance()->isCaptchaEnabled(),
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
    $root = Application::getDocumentRoot();
    $holder = Configuration::isInLocalHolder() ? Loader::LOCAL_HOLDER : Loader::BITRIX_HOLDER;
    $manifestPath = $root . '/' . $holder . '/js/anz/appointment/form-react/build/asset-manifest.json';
    $manifestContent = file_get_contents($manifestPath);
    if (!is_string($manifestContent))
    {
        throw new \RuntimeException('React asset manifest is unavailable.');
    }

    $manifest = json_decode($manifestContent, true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($manifest) || !is_array($manifest['files'] ?? null))
    {
        throw new \RuntimeException('React asset manifest has invalid format.');
    }

    $js = ltrim((string)($manifest['files']['main.js'] ?? ''), './');
    $css = ltrim((string)($manifest['files']['main.css'] ?? ''), './');
    if (
        !str_starts_with($js, 'static/js/main.')
        || !str_ends_with($js, '.js')
        || ($css !== '' && (!str_starts_with($css, 'static/css/main.') || !str_ends_with($css, '.css')))
    )
    {
        throw new \RuntimeException('React asset manifest contains invalid asset paths.');
    }

    return [
        'js' => 'build/' . $js,
        'css' => $css === '' ? '' : 'build/' . $css,
    ];
}
