<?php
namespace ANZ\Appointment\Component\Appointment\ItemsList\Provider;

use ANZ\Appointment\Model\RecordTable;
use ANZ\Appointment\UI\EntitySelector\UserProvider;
use Bitrix\Main\Filter\Settings;
use Bitrix\Main\Filter\TabletDataProvider;
use Bitrix\Main\ORM\Entity;

class FilterFieldsDataProvider extends TabletDataProvider
{
    public function __construct(
        private readonly Settings $settings,
        private readonly Entity   $entity,
        array $selectFields = [],
        array $defaultFields = [],
        bool $isDefaultShow = true
    )
    {
        parent::__construct($settings, $entity, $selectFields, $defaultFields, $isDefaultShow);
    }

    public function prepareFieldData($fieldID): ?array
    {
        if ($fieldID === RecordTable::FIELD_NAME_USER_ID)
        {
            return [
                'params' => [
                    'multiple' => true,
                    'dialogOptions' => [
                        'multiple' => true,
                        'preload' => true,
                        'enableSearch' => false,
                        'context' => $this->settings->getID(),
                        'entities' => [
                            [
                                'id' => UserProvider::ENTITY_ID,
                                'dynamicLoad' => true,
                                'dynamicSearch' => true,
                            ],
                        ]
                    ],
                ],
            ];
        }
        return parent::prepareFieldData($fieldID);
    }

    public function prepareFields(): array
    {
        $fields = parent::prepareFields();
        foreach ($this->entity->getFields() as $field)
        {
            if ($field->getName() === RecordTable::FIELD_NAME_USER_ID)
            {
                $fields[$field->getName()] = $this->createField(
                    $field->getName(),
                    [
                        'id' => $field->getName(),
                        'name' => $field->getTitle(),
                        'type' => 'entity_selector',
                        'default' => true,
                        'partial' => true
                    ]
                );
            }
        }
        return $fields;
    }
}
