<?php
namespace ANZ\Appointment\Config\Options;


use ANZ\Appointment\Core\Contract\Option\IOptionStorage;

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
