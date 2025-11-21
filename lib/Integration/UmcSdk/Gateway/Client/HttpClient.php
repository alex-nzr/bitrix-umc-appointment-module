<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 05.09.2025
 * ==================================================
*/

namespace ANZ\Appointment\Integration\UmcSdk\Gateway\Client;

use ANZ\BitUmc\SDK\Core\Dictionary\ClientScope;
use ANZ\BitUmc\SDK\Core\Dictionary\Protocol;
use Exception;

class HttpClient extends \ANZ\BitUmc\SDK\Client\HttpClient
{
    private static string $token;

    /**
     * @throws \Exception
     */
    public static function create(
        string $login,
        string $password,
        Protocol $protocol,
        string $address,
        string $baseName,
        ClientScope $scope,
        ?string $token = null
    ): static
    {
        if (is_null($token))
        {
            throw new Exception('Api token is required to use ' . static::class);
        }
        static::$token = $token;
        return parent::create($login, $password, $protocol, $address, $baseName, $scope);
    }
}