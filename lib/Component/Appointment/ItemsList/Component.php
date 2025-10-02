<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 02.10.2025
 * ==================================================
*/
namespace ANZ\Appointment\Component\Appointment\ItemsList;

use ANZ\Appointment\Component\Appointment\ItemsList\Grid\Grid as AnzGrid;
use ANZ\Appointment\Component\BaseGridComponent;
use ANZ\Appointment\Core\Exception\AccessDeniedException;
use ANZ\Appointment\Model\RecordTable;
use ANZ\Appointment\Service\Container;
use Bitrix\Main\Grid\Grid as BxGrid;
use Bitrix\Main\Grid\Settings;

class Component extends BaseGridComponent
{
    /**
     * @throws \Exception
     */
    protected function createGrid(): BxGrid
    {
        return new AnzGrid(new Settings([
            'ID' => static::getGridId(),
        ]));
    }

    protected function getTablet(): string
    {
        return RecordTable::class;
    }

    /**
     * @throws \Exception
     */
    public function checkRequirements(): bool
    {
        if (!Container::getInstance()->getUserPermissions()->checkReadPermissions())
        {
            throw new AccessDeniedException();
        }
        return true;
    }

    public static function getGridId(): string
    {
        return 'anz_appointment_list_grid';
    }
}