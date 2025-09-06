<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 06.09.2025
 * ==================================================
*/

namespace ANZ\Appointment\Service\Security;

use ANZ\Appointment\Config\Configuration;
use Bitrix\Main\Security\Random;

class Encryptor
{
    private const INIT_VECTOR_LENGTH = 15;

    private string $moduleId;

    /**
     * @throws \Exception
     */
    public function __construct(private readonly string $cipherAlgo = 'AES-256-CBC')
    {
        $this->moduleId = Configuration::getModuleId();
    }

    public function encrypt(string $value): string
    {
        $iv = Random::getString(self::INIT_VECTOR_LENGTH);
        if (extension_loaded('openssl'))
        {
            $encrypted = openssl_encrypt($value, $this->cipherAlgo, $this->moduleId, OPENSSL_RAW_DATA, $iv);
        }
        else
        {
            $encrypted = base64_encode($this->xorString($value, $this->moduleId));
        }

        return base64_encode($iv . $encrypted);
    }

    public function decrypt(string $value): string
    {
        if ($value === '')
        {
            return '';
        }

        $data = base64_decode($value);
        if ($data === false || strlen($data) < self::INIT_VECTOR_LENGTH)
        {
            return '';
        }

        $iv = substr($data, 0, self::INIT_VECTOR_LENGTH);
        $cipher = substr($data, self::INIT_VECTOR_LENGTH);
        if (extension_loaded('openssl'))
        {
            $decrypted = openssl_decrypt($cipher, $this->cipherAlgo, $this->moduleId, OPENSSL_RAW_DATA, $iv);
        }
        else
        {
            $decrypted = $this->xorString(base64_decode($cipher), $this->moduleId);
        }

        return (string)$decrypted;
    }

    private function xorString($string, $key): string
    {
        $out = '';
        for ($i = 0; $i < strlen($string); $i++)
        {
            $out .= $string[$i] ^ $key[$i % strlen($key)];
        }
        return $out;
    }
}