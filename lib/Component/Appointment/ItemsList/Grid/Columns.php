<?php
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
