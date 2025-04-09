<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 08.03.2025
 * ==================================================
*/
namespace ANZ\Appointment\Service\Cache;

use ANZ\BitUmc\SDK\Core\Trait\Singleton;

class Manager
{
    use Singleton;

    public function saveJsonCache(string $path, array $data): bool
    {
        return (bool)file_put_contents($path, json_encode($data));
    }

    public function getJsonCacheData(string $path): array
    {
        return json_decode(file_get_contents($path), true);
    }
}