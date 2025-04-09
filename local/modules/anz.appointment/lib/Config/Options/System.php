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
    const OPTION_KEY_LAST_UPDATED_URL_CONDITIONS_HASH = 'project_last_updated_url_conditions_hash';

    /**
     * @return array
     */
    public function getTabs(): array
    {
        return [];
    }
}