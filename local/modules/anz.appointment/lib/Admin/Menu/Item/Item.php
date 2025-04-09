<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 08.03.2025
 * ==================================================
*/
namespace ANZ\Appointment\Admin\Menu\Item;

use ANZ\Appointment\Internals\Contract\Menu\IMenuItem;
use Bitrix\Main\Security\Random;
use Exception;

abstract class Item implements IMenuItem
{
    const DEFAULT_SORT = 500;
    protected string $id;
    protected string $title;
    protected int $sort;
    protected string $icon;

    public function __construct(string $id, string $title, int $sort = self::DEFAULT_SORT, string $icon = '')
    {
        $this->id = $id;
        $this->title = $title;
        $this->sort = $sort;
        $this->icon = $icon;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @return int
     */
    public function getSort(): int
    {
        return $this->sort;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    public function setSort(int $sort): void
    {
        $this->sort = $sort;
    }

    /**
     * @throws \Exception
     */
    protected static function checkAndPrepareData(array $data): array
    {
        if (!key_exists('TITLE', $data) || empty($data['TITLE']))
        {
            throw new Exception('TITLE can not be empty');
        }

        return [
            'ID' => (!key_exists('ID', $data) || empty($data['ID']) || is_numeric($data['ID']))
                    ? $data['ID'] . Random::getString(10)
                    : (string)$data['ID'],
            'TITLE' => (string)$data['TITLE'],
            'SORT' => (key_exists('SORT', $data)) ? (int)$data['SORT'] : static::DEFAULT_SORT,
            'ICON' => (key_exists('ICON', $data)) ? (string)$data['ICON'] : ''
        ];
    }

    abstract public static function fromArray(array $data): Item;
    abstract public function getCompatibleData(): array;
    abstract public function isParent(): bool;
}