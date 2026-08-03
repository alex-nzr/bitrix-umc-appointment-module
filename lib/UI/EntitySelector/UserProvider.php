<?php

namespace ANZ\Appointment\UI\EntitySelector;

use ANZ\Appointment\Config\Configuration;
use Bitrix\Main\Application;
use Bitrix\Main\UserTable;
use Bitrix\UI\EntitySelector\BaseProvider;
use Bitrix\UI\EntitySelector\Dialog;
use Bitrix\UI\EntitySelector\Item;
use Bitrix\UI\EntitySelector\SearchQuery;
use CFile;

class UserProvider extends BaseProvider
{
    const ENTITY_ID = 'user-default';
    const COUNT_LIMIT = 30;

    protected string $defaultAvatar;

    /**
     * @throws \Exception
     */
    public function __construct(protected $options = [])
    {
        parent::__construct();
        $this->defaultAvatar = '/bitrix/panel/'.Configuration::getModuleId().'/ui-user.svg';
    }

    /**
     * @return bool
     */
    public function isAvailable(): bool
    {
        return true;
    }

    /**
     * @return \Bitrix\UI\EntitySelector\Item[]
     * @throws \Exception
     */
    public function getItems(array $ids = []): array
    {
        $items = [];

        foreach ($this->getItemsByIds($ids) as $item)
        {
            $items[] = $this->makeItem($item);
        }

        return $items;
    }

    /**
     * @throws \Exception
     */
    public function getSelectedItems(array $ids): array
    {
        return $this->getItemsByIds($ids);
    }

    /**
     * @throws \Exception
     */
    protected function getItemsByIds(array $ids = []): array
    {
        return $this->getUsersData(['@ID' => $ids]);
    }

    /**
     * @throws \Exception
     */
    protected function getItemsBySearchString(string $searchString): array
    {
        return $this->getUsersData([], $searchString);
    }

    /**
     * @throws \Exception
     */
    public function getUsersData(array $filter = [], string $searchString = ''): array
    {
        $select = [
            'ID',
            'EMAIL',
            'PERSONAL_PHONE',
            'SECOND_NAME',
            'NAME',
            'LAST_NAME',
            'PERSONAL_PHOTO',
            'ACTIVE'
        ];

        if (strlen($searchString) > 0)
        {
            $filter[] = [
                'LOGIC' => 'OR',
                ['%NAME' => $searchString],
                ['%LAST_NAME' => $searchString],
                ['%SECOND_NAME' => $searchString],
                ['%LOGIN' => $searchString],
                ['%EMAIL' => $searchString],
                ['%PERSONAL_PHONE' => $searchString],
            ];
        }

        return UserTable::query()
            ->setSelect($select)
            ->setFilter($filter)
            ->setLimit(static::COUNT_LIMIT)
            ->setOrder(['ID' => 'ASC'])
            ->fetchAll();
    }

    protected function makeItem(array $item): Item
    {
        $uiItem = new Item([
            'id' => $item['ID'],
            'entityId' => static::ENTITY_ID,
            'entityType' => $item['ACTIVE'] === 'Y' ? 'user' : 'inactive',
            'title' => $this->formatUserName($item),
            'avatar' => $this->makeUserAvatar($item),
            'customData' => []
        ]);

        $uiItem->setBadges([
            [
                'title' => $item['EMAIL'],
                'textColor' => '#000',
                'bgColor' => 'lightgrey',
            ],
            [
                'title' => $item['PERSONAL_PHONE'],
                'textColor' => '#000',
                'bgColor' => 'lightgrey',
            ],
        ]);

        return $uiItem;
    }

    protected function formatUserName(array $item): string
    {
        if ($item['SECOND_NAME'])
        {
            return $item['NAME'] . " " . $item['SECOND_NAME'] . " " . $item['LAST_NAME'];
        }

        return $item['NAME'] . " " . $item['LAST_NAME'];
    }

    protected function makeUserAvatar(array $item): ?string
    {
        if ($item['PERSONAL_PHOTO'])
        {
            $avatar = CFile::resizeImageGet(
                $item['PERSONAL_PHOTO'],
                ['width' => 100, 'height' => 100],
                BX_RESIZE_IMAGE_EXACT
            );
            $avatarSrc = $avatar['src'] ?: null;
        }
        else
        {
            if (is_file(Application::getDocumentRoot() . $this->defaultAvatar))
            {
                $avatarSrc = $this->defaultAvatar;
            }
            else
            {
                $avatarSrc = null;
            }
        }

        return $avatarSrc;
    }

    /**
     * @throws \Exception
     */
    public function doSearch( SearchQuery $searchQuery, Dialog $dialog): void
    {
        $items = $this->getItemsBySearchString($searchQuery->getQuery());

        $limitExceeded = count($items) >= static::COUNT_LIMIT;
        if ($limitExceeded)
        {
            $searchQuery->setCacheable(false);
        }

        if (count($items) > 0)
        {
            foreach ($items as $item)
            {
                $dialog->addItem(
                    $this->makeItem($item)
                );
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function fillDialog(Dialog $dialog): void
    {
        $preloadedUsers = $this->getUsersData();
        if (count($preloadedUsers) < static::COUNT_LIMIT)
        {
            $entity = $dialog->getEntity(static::ENTITY_ID);
            $entity?->setDynamicSearch(false);
        }

        $recentUsers = [];
        $recentItems = $dialog->getRecentItems()->getEntityItems(static::ENTITY_ID);
        $recentIds = array_map('intval', array_keys($recentItems));
        $this->fillRecentUsers($recentUsers, $recentIds, $preloadedUsers);

        if (count($recentUsers) < static::COUNT_LIMIT)
        {
            $recentGlobalItems = $dialog->getGlobalRecentItems()->getEntityItems(static::ENTITY_ID);
            $recentGlobalIds = [];

            if (count($recentGlobalItems) > 0)
            {
                $recentGlobalIds = array_map('intval', array_keys($recentGlobalItems));
                $recentGlobalIds = array_values(array_diff($recentGlobalIds, array_column($recentUsers, 'ID')));
                $recentGlobalIds = array_slice($recentGlobalIds, 0, self::COUNT_LIMIT - count($recentUsers));
            }

            $this->fillRecentUsers($recentUsers, $recentGlobalIds, $preloadedUsers);
        }

        foreach ($preloadedUsers as $preloadedUser)
        {
            $recentUsers[] = $preloadedUser;
        }

        foreach ($recentUsers as $recentUser)
        {
            $dialog->addRecentItem($this->makeItem($recentUser));
        }
    }

    /**
     * @throws \Exception
     */
    protected function fillRecentUsers(array &$recentUsers, array $recentIds, array &$preloadedUsers): void
    {
        if (count($recentIds) === 0)
        {
            return;
        }

        $ids = array_values(array_diff($recentIds, array_column($preloadedUsers, 'ID')));
        if (count($ids) > 0)
        {
            $users = $this->getUsersData(['@ID' => $ids]);
            foreach ($users as $user)
            {
                $preloadedUsers[] = $user;
            }
        }

        foreach ($recentIds as $recentId)
        {
            $filteredUsers = array_filter($preloadedUsers, fn(array $user) => (int)$user['ID'] === (int)$recentId);
            if (count($filteredUsers) > 0)
            {
                $recentUsers[] = current($filteredUsers);
            }
        }
    }
}
