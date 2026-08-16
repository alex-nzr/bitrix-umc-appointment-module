<?php

namespace ANZ\Appointment\Service\Security\Captcha;

use ANZ\Appointment\Core\Exception\PublicRequestException;
use Bitrix\Main\SystemException;

class BitrixCaptcha
{
    /**
     * @return array{captchaSid: string, imageUrl: string}
     * @throws SystemException
     */
    public function create(): array
    {
        global $APPLICATION;

        if (!is_object($APPLICATION) || !method_exists($APPLICATION, 'CaptchaGetCode'))
        {
            throw new SystemException('Bitrix CAPTCHA is unavailable');
        }

        $captchaSid = $APPLICATION->CaptchaGetCode();
        if (!is_string($captchaSid) || $captchaSid === '')
        {
            throw new SystemException('Unable to create CAPTCHA');
        }

        return [
            'captchaSid' => $captchaSid,
            'imageUrl' => '/bitrix/tools/captcha.php?captcha_sid=' . rawurlencode($captchaSid),
        ];
    }

    /**
     * @throws SystemException
     */
    public function assertValid(string $captchaSid, string $captchaWord): void
    {
        global $APPLICATION;

        if (
            trim($captchaSid) === ''
            || trim($captchaWord) === ''
            || !is_object($APPLICATION)
            || !method_exists($APPLICATION, 'CaptchaCheckCode')
            || !$APPLICATION->CaptchaCheckCode($captchaWord, $captchaSid)
        )
        {
            throw new PublicRequestException('Invalid CAPTCHA');
        }
    }
}
