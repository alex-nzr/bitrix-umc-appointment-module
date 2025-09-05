<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 08.03.2025
 * ==================================================
*/
namespace ANZ\Appointment\Admin\Menu\Item;

use ANZ\Appointment\Core\Contract\Menu\IMenuItem;

class ParentItem extends Item
{
    protected array $itemsData = [];
    protected array $items = [];

    public function __construct(string $id, string $title, array $itemsData = [], int $sort = 500, string $icon = '')
    {
        parent::__construct($id, $title, $sort, $icon);
        $this->itemsData = $itemsData;
    }

    /**
     * @throws \Exception
     */
    public static function fromArray(array $data): Item | ParentItem
    {
        $preparedData = static::checkAndPrepareData($data);
        $items = is_array($data['ITEMS']) ? $data['ITEMS'] : [];
        return new static(
            $preparedData['ID'], $preparedData['TITLE'], $items, $preparedData['SORT'], $preparedData['ICON']
        );
    }

    /**
     * @return array
     */
    public function getCompatibleData(): array
    {
        return [
            'menu_id' => $this->getId(),
            'text' => $this->getTitle(),
            'title' => $this->getTitle(),
            'sort' => $this->getSort(),
            'items_id' => $this->getItemsId(),
            'icon' => $this->getIcon(),
            'items' => array_map(function(IMenuItem $item){ return $item->getCompatibleData(); }, $this->getItems()),
        ];
    }

    /**
     * @param \ANZ\Appointment\Core\Contract\Menu\IMenuItem $item
     * @return void
     */
    public function addChildItem(IMenuItem $item): void
    {
        $this->items[] = $item;
    }

    /**
     * @return IMenuItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return string
     */
    public function getItemsId(): string
    {
        return $this->id . '_items';
    }

    /**
     * @return array
     */
    public function getItemsData(): array
    {
        return $this->itemsData;
    }

    /**
     * @return bool
     */
    public function isParent(): bool
    {
        return true;
    }
}