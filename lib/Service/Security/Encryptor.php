<?php

namespace ANZ\Appointment\Service\Security;

use Bitrix\Main\Config\Configuration as BitrixConfiguration;
use Bitrix\Main\Security\Cipher;
use Bitrix\Main\Security\SecurityException;
use RuntimeException;

class Encryptor
{
    private const VALUE_PREFIX = 'v2:';

    private ?Cipher $cipher = null;

    /**
     * @throws RuntimeException
     * @throws SecurityException
     */
    public function encrypt(string $value): string
    {
        if ($value === '')
        {
            return '';
        }

        return self::VALUE_PREFIX . base64_encode($this->getCipher()->encrypt($value, $this->getCryptoKey()));
    }

    public function decrypt(string $value): string
    {
        if ($value === '')
        {
            return '';
        }

        try
        {
            if (!str_starts_with($value, self::VALUE_PREFIX))
            {
                return '';
            }

            $encrypted = base64_decode(substr($value, strlen(self::VALUE_PREFIX)), true);
            if ($encrypted === false)
            {
                return '';
            }

            return $this->getCipher()->decrypt($encrypted, $this->getCryptoKey());
        }
        catch (SecurityException|RuntimeException)
        {
            return '';
        }
    }

    private function getCryptoKey(): string
    {
        $cryptoSettings = BitrixConfiguration::getValue('crypto');
        $cryptoKey = is_array($cryptoSettings) ? ($cryptoSettings['crypto_key'] ?? '') : '';

        if (!is_string($cryptoKey) || $cryptoKey === '')
        {
            throw new RuntimeException('Bitrix crypto key is not configured.');
        }

        return $cryptoKey;
    }

    /**
     * @return \Bitrix\Main\Security\Cipher
     */
    private function getCipher(): Cipher
    {
        return $this->cipher ??= new Cipher();
    }

}
