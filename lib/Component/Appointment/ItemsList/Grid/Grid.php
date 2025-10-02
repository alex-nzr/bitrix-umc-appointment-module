<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 02.10.2025
 * ==================================================
*/

namespace ANZ\Appointment\Component\Appointment\ItemsList\Grid;

use ANZ\Appointment\Component\Appointment\ItemsList\Provider\FilterFieldsDataProvider;
use ANZ\Appointment\Component\Appointment\ItemsList\Provider\GridColumnsProvider;
use ANZ\Appointment\Model\RecordTable;
use Bitrix\Main\Filter\Filter;
use Bitrix\Main\Filter\Settings;
use Bitrix\Main\Grid\Pagination\PaginationFactory;
use Bitrix\Main\Grid\TabletGrid;
use Bitrix\Main\ORM\Fields\Field;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\UI\PageNavigation;

class Grid extends TabletGrid
{
    protected function getTabletClass(): string
    {
        return RecordTable::class;
    }

    protected function createColumns(): Columns
    {
        return new Columns(
            new GridColumnsProvider(
                $this->getEntity(),
                array_filter(
                    array_map(
                        fn(Field $field) => $field instanceof Reference ? null : $field->getName(),
                        $this->getEntity()->getFields()
                    )
                ),
            )
        );
    }

    protected function createRows(): Rows
    {
        return new Rows($this->getVisibleColumnsIds());
    }

    /**
     * @throws \Exception
     */
    protected function createFilter(): Filter
    {
        return new Filter(
            $this->getId(),
            new FilterFieldsDataProvider(
                new Settings([
                    'ID' => $this->getId(),
                ]),
                $this->getEntity(),
                $this->getVisibleColumnsIds(),
                [RecordTable::FIELD_NAME_ID, RecordTable::FIELD_NAME_UID],
                false
            )
        );
    }

    protected function createPagination(): ?PageNavigation
    {
        return (new PaginationFactory($this, $this->getPaginationStorage()))->create();
    }
}