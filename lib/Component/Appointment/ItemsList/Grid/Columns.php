<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 02.10.2025
 * ==================================================
*/
namespace ANZ\Appointment\Component\Appointment\ItemsList\Grid;

class Columns extends \Bitrix\Main\Grid\Column\Columns
{
    protected function prepareColumns(array $columns): array
    {
        foreach ($columns as $column)
        {
            if ($column->getId() === 'ID')
            {
                $column->setNecessary(true);
            }
        }

        return $columns;
    }
}