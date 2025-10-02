<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 02.10.2025
 * ==================================================
*/
namespace ANZ\Appointment\Component;

use Bitrix\Main\Filter\Component\ComponentParams as FilterComponentParams;
use Bitrix\Main\Grid\Component\ComponentParams as GridComponentParams;
use Bitrix\Main\Grid\Component\TabletGridComponent;
use Bitrix\Main\Grid\Grid;

abstract class BaseGridComponent extends TabletGridComponent
{
    use ComponentTrait;

    abstract protected function createGrid(): Grid;
    abstract protected function getTablet(): string;
    abstract public static function getGridId(): string;

    public function getResult(): array
    {
        $grid = $this->createGrid();
        $grid->processRequest();
        $this->fillPagination($grid);
        $this->fillRows($grid);

        return [
            'FILTER_PARAMS' => FilterComponentParams::get($grid->getFilter(), [
                'ENABLE_LIVE_SEARCH' => false,
                'GRID_ID' => $grid->getId()
            ]),
            'GRID_PARAMS' => GridComponentParams::get($grid, [
                'SHOW_CHECK_ALL_CHECKBOXES' => true,
                'ALLOW_HORIZONTAL_SCROLL'   => true,
                'ALLOW_PIN_HEADER'          => true,
            ]),
        ];
    }
}