<?php
namespace ANZ\Appointment\Admin\Menu;

use ANZ\Appointment\Admin\Menu\Item\LinkItem;
use ANZ\Appointment\Admin\Menu\Item\ParentItem;
use Exception;

class Manager
{
    protected bool $adminMenuCustomized = false;
    protected array $additionalAdminMenu = [];

    /**
     * @param array $globalAdminMenu
     * @return void
     * @throws \Exception
     */
    public function processGlobalMenu(array &$globalAdminMenu /*, array &$arModuleMenu*/): void
    {
        if (!$this->adminMenuCustomized)
        {
            $additionalMenu = (new Storage())->getAdditionalMenuStructure();
            if (!empty($additionalMenu))
            {
                if ($this->checkRootItems($additionalMenu))
                {
                    $this->processItems($additionalMenu);
                }

                $globalAdminMenu = array_merge($globalAdminMenu, $this->additionalAdminMenu);

                $this->adminMenuCustomized = true;
            }
        }
    }

    /**
     * @throws \Exception
     */
    protected function processItems(array $subMenuItems, ParentItem $parentItem = null): void
    {
        $isRootLevel = is_null($parentItem);

        foreach ($subMenuItems as $subMenuItemData)
        {
            if (is_array($subMenuItemData))
            {
                $item = $this->buildItem($subMenuItemData);
                if ($item->isParent())
                {
                    $this->processItems($item->getItemsData(), $item);
                }

                if ($isRootLevel)
                {
                    $this->additionalAdminMenu[$item->getId()] = $item->getCompatibleData();
                }
                else
                {
                    $parentItem->addChildItem($item);
                }
            }
        }
    }

    /**
     * @throws \Exception
     */
    protected function buildItem(array $itemData): ParentItem | LinkItem
    {
        if (key_exists('ITEMS', $itemData))
        {
            $item = ParentItem::fromArray($itemData);
        }
        else
        {
            $item = LinkItem::fromArray($itemData);
        }

        return $item;
    }

    /**
     * @throws \Exception
     */
    protected function checkRootItems(array $rootItems): bool
    {
        foreach ($rootItems as $rootItemData)
        {
            if (!is_array($rootItemData))
            {
                throw new Exception('Root item must be an array');
            }

            if (!is_array($rootItemData['ITEMS']) || empty($rootItemData['ITEMS']))
            {
                throw new Exception('ITEMS in root item must be a not empty array');
            }
        }

        return true;
    }
}
