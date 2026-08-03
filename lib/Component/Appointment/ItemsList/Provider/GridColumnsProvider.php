<?php

namespace ANZ\Appointment\Component\Appointment\ItemsList\Provider;

use ANZ\Appointment\Model\RecordTable;
use Bitrix\Main\Grid\Column\Column;
use Bitrix\Main\Grid\Column\DataProvider\TabletColumnsProvider;
use Bitrix\Main\Grid\Column\Type;
use Bitrix\Main\ORM\Entity;
use Bitrix\Main\ORM\Fields\Field;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Fields\ScalarField;

class GridColumnsProvider extends TabletColumnsProvider
{
    public function __construct(
        private readonly Entity $ormEntity,
        private readonly array $selectFields = [],
        private readonly array $defaultFields = [],
        private readonly bool   $isDefaultShow = true,
    )
    {
        parent::__construct($ormEntity, $selectFields, $defaultFields, $isDefaultShow);
    }

    /**
     * @throws \Exception
     */
    public function prepareColumns(): array
    {
        $result =  parent::prepareColumns();

        foreach ($this->ormEntity->getFields() as $field)
        {
            if (key_exists($field->getName(), $result)
                || !in_array($field->getName(), $this->selectFields))
            {
                continue;
            }

            if (!($field instanceof ScalarField) && !($field instanceof Reference))
            {
                $column = (new Column($field->getName()))
                    ->setName($field->getTitle() ?: $field->getName())
                    ->setType($this->getColumnType($field))
                    ->setSort($field->getName())
                    ->setEditable(false)
                    ->setDefault(in_array($field->getName(), $this->defaultFields) || $this->isDefaultShow);

                $result[$column->getId()] = $column;
            }
        }

        return $result;
    }

    protected function getColumnType(Field $field): string
    {
        return match ($field->getName())
        {
            RecordTable::FIELD_NAME_DAYS_LEFT => Type::INT,
            default => $field instanceof ScalarField ? parent::getColumnTypeByField($field) : null,
        };
    }
}
