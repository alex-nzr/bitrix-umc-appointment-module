<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 08.03.2025
 * ==================================================
*/
namespace ANZ\Appointment\Config\Options;


use ANZ\Appointment\Internals\Contract\Option\IOptionStorage;

/**
 * Hidden from public access system options
 */
class System implements IOptionStorage
{
    /**
     * @return array
     */
    public function getTabs(): array
    {
        return [];
    }
}