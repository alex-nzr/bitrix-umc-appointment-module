<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.02.2025
 * ==================================================
*/

namespace ANZ\Appointment\Integration\UmcSdk\Factory;

use ANZ\Appointment\Integration\UmcSdk\Service\Exchange\Soap;
use ANZ\BitUmc\SDK\Core\Dictionary\ClientScope;
use ANZ\BitUmc\SDK\Service\Exchange\Http;

class Exchange extends \ANZ\BitUmc\SDK\Factory\Exchange
{
    public function create(): Http|Soap
    {
        $serviceClass = match ($this->client->getScope()) {
            ClientScope::HTTP_SERVICE => parent::create(),
            ClientScope::WEB_SERVICE => Soap::class,
        };

        return (new $serviceClass($this->client));
    }
}