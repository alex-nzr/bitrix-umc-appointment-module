<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 03.10.2025
 * ==================================================
*/
namespace ANZ\Appointment\Component\Appointment\ItemsList\Provider\EntitySelector;

use Bitrix\Main\EO_User;
use Bitrix\Main\EO_User_Collection;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Objectify\Collection;
use Bitrix\Main\ORM\Objectify\EntityObject;
use Bitrix\Main\ORM\Query\Filter\Helper;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Main\Search\Content;
use Bitrix\Main\UserIndexTable;
use Bitrix\Main\UserTable;
use Bitrix\Main\UserUtils;
use Bitrix\UI\EntitySelector\BaseProvider;
use Bitrix\UI\EntitySelector\Dialog;
use Bitrix\UI\EntitySelector\Item;
use Bitrix\UI\EntitySelector\SearchQuery;
use CFile;
use CSite;
use CUser;

class UserProvider extends BaseProvider
{
    public const ENTITY_ID = 'anz-user';
    protected const MAX_USERS_COUNT = 50;

    public function __construct(array $options = [])
    {
        parent::__construct();
        $this->prepareOptions($options);
    }

    protected function prepareOptions(array $options = []): void
    {
        $this->options['nameTemplate'] = CSite::getNameFormat(false);
        $this->options['analyticsSource'] = 'userProvider';
        if (isset($options['selectFields']) && is_array($options['selectFields']))
        {
            $selectFields = [];
            $allowedFields = static::getAllowedFields();
            foreach ($options['selectFields'] as $field)
            {
                if (is_string($field) && array_key_exists($field, $allowedFields))
                {
                    $selectFields[] = $field;
                }
            }

            $this->options['selectFields'] = array_unique($selectFields);
        }
    }

    public function isAvailable(): bool
    {
        return true;
    }

    /**
     * @throws \Exception
     */
    public function getItems(array $ids): array
    {
        return $this->getUserItems([
            'userId' => $ids
        ]);
    }

    /**
     * @throws \Exception
     */
    public function getSelectedItems(array $ids): array
    {
        return $this->getUserItems([
            'userId' => $ids,
            'ignoreUserWhitelist' => true,
            'activeUsers' => null // to see fired employees
        ]);
    }

    /**
     * @throws \Exception
     */
    public function fillDialog(Dialog $dialog): void
    {
        $preloadedUsers = $this->getPreloadedUsersCollection();

        if ($preloadedUsers->count() < self::MAX_USERS_COUNT)
        {
            $entity = $dialog->getEntity(static::ENTITY_ID);
            $entity?->setDynamicSearch(false);
        }

        $recentUsers = new EO_User_Collection;

        $recentItems = $dialog->getRecentItems()->getEntityItems(static::ENTITY_ID);
        $recentIds = array_map('intval', array_keys($recentItems));
        $this->fillRecentUsers($recentUsers, $recentIds, $preloadedUsers);

        if ($recentUsers->count() < self::MAX_USERS_COUNT)
        {
            $recentGlobalItems = $dialog->getGlobalRecentItems()->getEntityItems(static::ENTITY_ID);
            $recentGlobalIds = [];

            if (!empty($recentGlobalItems))
            {
                $recentGlobalIds = array_map('intval', array_keys($recentGlobalItems));
                $recentGlobalIds = array_values(array_diff($recentGlobalIds, $recentUsers->getIdList()));
                $recentGlobalIds = array_slice($recentGlobalIds, 0, self::MAX_USERS_COUNT - $recentUsers->count());
            }

            $this->fillRecentUsers($recentUsers, $recentGlobalIds, $preloadedUsers);
        }

        foreach ($preloadedUsers as $preloadedUser)
        {
            $recentUsers->add($preloadedUser);
        }

        $dialog->addRecentItems($this->makeUserItems($recentUsers));
    }

    /**
     * @throws \Exception
     */
    protected function getPreloadedUsersCollection(): Collection
    {
        return $this->getUserCollection([
            'order' => ['ID' => 'asc'],
            'limit' => self::MAX_USERS_COUNT
        ]);
    }

    /**
     * @throws \Exception
     */
    private function fillRecentUsers(
        Collection|EO_User_Collection $recentUsers,
        array $recentIds,
        Collection|EO_User_Collection $preloadedUsers
    ): void
    {
        if (count($recentIds) < 1)
        {
            return;
        }

        $ids = array_values(array_diff($recentIds, $preloadedUsers->getIdList()));
        if (!empty($ids))
        {
            $users = $this->getUserCollection(['userId' => $ids]);
            foreach ($users as $user)
            {
                $preloadedUsers->add($user);
            }
        }

        foreach ($recentIds as $recentId)
        {
            $user = $preloadedUsers->getByPrimary($recentId);
            if ($user)
            {
                $recentUsers->add($user);
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function doSearch(SearchQuery $searchQuery, Dialog $dialog): void
    {
        $atom = '=_0-9a-z+~\'!$&*^`|\\#%/?{}-';
        $isEmailLike = (bool)preg_match('#^['.$atom.']+(\\.['.$atom.']+)*@#i', $searchQuery->getQuery());
        $limit = 100;

        if ($isEmailLike)
        {
            $items = $this->getUserItems([
                'searchByEmail' => $searchQuery->getQuery(),
                'myEmailUsers' => false,
                'limit' => $limit
            ]);
        }
        else
        {
            $items = $this->getUserItems([
                'searchQuery' => $searchQuery->getQuery(),
                'limit' => $limit
            ]);
        }

        $limitExceeded = $limit <= count($items);
        if ($limitExceeded)
        {
            $searchQuery->setCacheable(false);
        }

        $dialog->addItems($items);
    }

    /**
     * @throws \Exception
     */
    public function getUserCollection(array $options = []): Collection
    {
        $dialogOptions = $this->getOptions();
        $options = array_merge($dialogOptions, $options);

        $ignoreUserWhitelist = isset($options['ignoreUserWhitelist']) && $options['ignoreUserWhitelist'] === true;
        if (!empty($dialogOptions['userId']) && !$ignoreUserWhitelist)
        {
            $options['userId'] = $dialogOptions['userId'];
        }

        return static::getUsers($options);
    }

    /**
     * @throws \Exception
     */
    public function getUserItems(array $options = []): array
    {
        return $this->makeUserItems($this->getUserCollection($options), $options);
    }

    public function makeUserItems(Collection|EO_User_Collection $users, array $options = []): array
    {
        return self::makeItems($users, array_merge($this->getOptions(), $options));
    }

    public static function getAllowedFields(): array
    {
        static $fields = null;

        if ($fields !== null)
        {
            return $fields;
        }

        $fields = [
            'lastName' => 'LAST_NAME',
            'name' => 'NAME',
            'secondName' => 'SECOND_NAME',
            'login' => 'LOGIN',
            'email' => 'EMAIL',
            'title' => 'TITLE',
            'position', 'WORK_POSITION',
            'lastLogin' => 'LAST_LOGIN',
            'dateRegister' => 'DATE_REGISTER',
            'lastActivityDate' => 'LAST_ACTIVITY_DATE',
            'online' => 'IS_ONLINE',
            'profession' => 'PERSONAL_PROFESSION',
            'www' => 'PERSONAL_WWW',
            'birthday' => 'PERSONAL_BIRTHDAY',
            'icq' => 'PERSONAL_ICQ',
            'phone' => 'PERSONAL_PHONE',
            'fax' => 'PERSONAL_FAX',
            'mobile' => 'PERSONAL_MOBILE',
            'pager' => 'PERSONAL_PAGER',
            'street' => 'PERSONAL_STREET',
            'city' => 'PERSONAL_CITY',
            'state' => 'PERSONAL_STATE',
            'zip' => 'PERSONAL_ZIP',
            'mailbox' => 'PERSONAL_MAILBOX',
            'country' => 'PERSONAL_COUNTRY',
            'timeZoneOffset' => 'TIME_ZONE_OFFSET',
            'company' => 'WORK_COMPANY',
            'workPhone' => 'WORK_PHONE',
            'workDepartment' => 'WORK_DEPARTMENT',
            'workPosition' => 'WORK_POSITION',
            'workCity' => 'WORK_CITY',
            'workCountry' => 'WORK_COUNTRY',
            'workStreet' => 'WORK_STREET',
            'workState' => 'WORK_STATE',
            'workZip' => 'WORK_ZIP',
            'workMailbox' => 'WORK_MAILBOX',
        ];

        foreach ($fields as $id => $dbName)
        {
            if (mb_strpos($dbName, 'PERSONAL_') === 0)
            {
                $fields['personal' . ucfirst($id)] = $dbName;
            }

            $fields[$dbName] = $dbName;
        }

        $intranetInstalled = ModuleManager::isModuleInstalled('intranet');
        if ($intranetInstalled)
        {
            $userFields = $GLOBALS['USER_FIELD_MANAGER']->GetUserFields('USER');
            $allowedUserFields = [
                'ufPhoneInner' => 'UF_PHONE_INNER',
                'ufDistrict' => 'UF_DISTRICT',
                'ufSkype' => 'UF_SKYPE',
                'ufSkypeLink' => 'UF_SKYPE_LINK',
                'ufZoom' => 'UF_ZOOM',
                'ufTwitter' => 'UF_TWITTER',
                'ufFacebook' => 'UF_FACEBOOK',
                'ufLinkedin' => 'UF_LINKEDIN',
                'ufXing' => 'UF_XING',
                'ufWebSites' => 'UF_WEB_SITES',
                'ufSkills' => 'UF_SKILLS',
                'ufInterests' => 'UF_INTERESTS',
                'ufEmploymentDate' => 'UF_EMPLOYMENT_DATE',
            ];

            foreach ($allowedUserFields as $id => $dbName)
            {
                if (array_key_exists($dbName, $userFields))
                {
                    $fields[$id] = $dbName;
                    $fields[$dbName] = $dbName;
                }
            }
        }

        return $fields;
    }

    /**
     * @throws \Exception
     */
    public static function getUsers(array $options = []): ?Collection
    {
        $query = static::getQuery($options);
        $result = $query->exec();

        return $result->fetchCollection();
    }

    /**
     * @throws \Exception
     */
    protected static function getQuery(array $options = []): Query
    {
        $selectFields = [
            'ID', 'ACTIVE', 'LAST_NAME', 'NAME', 'SECOND_NAME', 'LOGIN', 'EMAIL', 'TITLE',
            'PERSONAL_GENDER', 'PERSONAL_PHOTO', 'WORK_POSITION',
            'CONFIRM_CODE', 'EXTERNAL_AUTH_ID'
        ];

        if (isset($options['selectFields']) && is_array($options['selectFields']))
        {
            $allowedFields = static::getAllowedFields();
            foreach ($options['selectFields'] as $field)
            {
                if (is_string($field) && array_key_exists($field, $allowedFields))
                {
                    $selectFields[] = $allowedFields[$field];
                }
            }
        }

        $query = UserTable::query();
        $query->setSelect(array_unique($selectFields));

        $intranetInstalled = ModuleManager::isModuleInstalled('intranet');
        if ($intranetInstalled)
        {
            $query->addSelect('UF_DEPARTMENT');
        }

        $activeUsers = array_key_exists('activeUsers', $options) ? $options['activeUsers'] : true;
        if (is_bool($activeUsers))
        {
            $query->where('ACTIVE', $activeUsers ? 'Y' : 'N');
        }

        if (!empty($options['searchQuery']) && is_string($options['searchQuery']))
        {
            $query->registerRuntimeField(
                new Reference(
                    'USER_INDEX',
                    UserIndexTable::class,
                    Join::on('this.ID', 'ref.USER_ID'),
                    ['join_type' => 'INNER']
                )
            );

            $query->whereMatch(
                'USER_INDEX.SEARCH_USER_CONTENT',
                Helper::matchAgainstWildcard(
                    Content::prepareStringToken($options['searchQuery']), '*', 1
                )
            );
        }
        else if (!empty($options['searchByEmail']) && is_string($options['searchByEmail']))
        {
            $query->whereLike('EMAIL', $options['searchByEmail'].'%');
        }

        $userIds = self::prepareUserIds($options['userId'] ?? []);
        $notUserIds = self::prepareUserIds($options['!userId'] ?? []);

        if (!empty($userIds))
        {
            $query->whereIn('ID', $userIds);
        }

        if (!empty($notUserIds))
        {
            $query->whereNotIn('ID', $notUserIds);
        }


        $query->setLimit(self::MAX_USERS_COUNT);

        return $query;
    }

    private static function prepareUserIds($items): array
    {
        $ids = [];
        if (is_array($items) && !empty($items))
        {
            foreach ($items as $id)
            {
                if ((int)$id > 0)
                {
                    $ids[] = (int)$id;
                }
            }

            $ids = array_unique($ids);
        }
        else if (!is_array($items) && (int)$items > 0)
        {
            $ids = [(int)$items];
        }

        return $ids;
    }

    /**
     * @throws \Exception
     */
    public static function getUser(int $userId, array $options = []): ?EntityObject
    {
        $options['userId'] = $userId;
        $users = static::getUsers($options);

        return $users->count() ? $users->getAll()[0] : null;
    }

    public static function makeItems(Collection $users, array $options = []): array
    {
        $result = [];
        foreach ($users as $user)
        {
            $result[] = static::makeItem($user, $options);
        }

        return $result;
    }

    public static function makeItem(EO_User $user, array $options = []): Item
    {
        $customData = [];
        foreach (['name', 'lastName', 'secondName', 'email', 'login'] as $field)
        {
            if (!empty($user->{'get'.$field}()))
            {
                $customData[$field] = $user->{'get'.$field}();
            }
        }

        if (!empty($user->getPersonalGender()))
        {
            $customData['gender'] = $user->getPersonalGender();
        }

        if (!empty($user->getWorkPosition()))
        {
            $customData['position'] = $user->getWorkPosition();
        }

        $userType = self::getUserType($user);

        if (isset($options['selectFields']) && is_array($options['selectFields']))
        {
            $userData = $user->collectValues();
            $allowedFields = static::getAllowedFields();
            foreach ($options['selectFields'] as $field)
            {
                if (!is_string($field))
                {
                    continue;
                }

                $dbName = $allowedFields[$field] ?? null;
                $value = $userData[$dbName] ?? null;
                if (!empty($value))
                {
                    if ($field === 'country' || $field === 'workCountry')
                    {
                        $value = UserUtils::getCountryValue(['VALUE' => $value]);
                    }

                    $customData[$field] = $value;
                }
            }
        }

        if (isset($options['showLogin']) && $options['showLogin'] === false)
        {
            unset($customData['login']);
        }

        if (isset($options['showEmail']) && $options['showEmail'] === false)
        {
            unset($customData['email']);
        }

        return new Item([
            'id' => $user->getId(),
            'entityId' => static::ENTITY_ID,
            'entityType' => $userType,
            'title' => self::formatUserName($user, $options),
            'avatar' => self::makeUserAvatar($user),
            'customData' => $customData,
            'tabs' => static::getTabsNames(),
        ]);
    }

    protected static function getTabsNames(): array
    {
        return [static::ENTITY_ID];
    }

    public static function getUserType(EO_User $user): string
    {
        if (!$user->getActive())
        {
            $type = 'inactive';
        }
        else if ($user->getExternalAuthId() === 'email')
        {
            $type = 'email';
        }
        else
        {
            $type = 'user';
        }

        return $type;
    }

    public static function formatUserName(EO_User $user, array $options = []): string
    {
        return CUser::formatName(
            !empty($options['nameTemplate']) ? $options['nameTemplate'] : CSite::getNameFormat(false),
            [
                'NAME' => $user->getName(),
                'LAST_NAME' => $user->getLastName(),
                'SECOND_NAME' => $user->getSecondName(),
                'LOGIN' => $user->getLogin(),
                'EMAIL' => $user->getEmail(),
                'TITLE' => $user->getTitle(),
            ],
            true,
            false
        );
    }

    public static function makeUserAvatar(EO_User $user): ?string
    {
        if (empty($user->getPersonalPhoto()))
        {
            return null;
        }

        $avatar = CFile::resizeImageGet(
            $user->getPersonalPhoto(),
            ['width' => 100, 'height' => 100],
            BX_RESIZE_IMAGE_EXACT
        );

        return !empty($avatar['src']) ? $avatar['src'] : null;
    }
}