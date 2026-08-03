<?php
namespace ANZ\Appointment\Admin\Menu\Item;

class LinkItem extends Item
{
    protected string $id;
    protected string $title;
    protected string $url;
    protected int $sort = 1000;
    protected string $icon = '';

    public function __construct(
        string $id,
        string $title,
        string $url,
        int $sort = 1000,
        string $icon = ''
    ){
        parent::__construct($id, $title, $sort, $icon);
        $this->url = $url;
    }

    /**
     * @throws \Exception
     */
    public static function fromArray(array $data): Item | ParentItem
    {
        $preparedData = static::checkAndPrepareData($data);
        return new static(
            $preparedData['ID'], $preparedData['TITLE'], (string)$data['URL'], $preparedData['SORT'], $preparedData['ICON']
        );
    }

    /**
     * @return array
     */
    public function getCompatibleData(): array
    {
        return [
            'text' => $this->getTitle(),
            'title' => $this->getTitle(),
            'sort' => $this->getSort(),
            'url' => $this->getUrl(),
            'icon' => $this->getIcon()
        ];
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return bool
     */
    public function isParent(): bool
    {
        return false;
    }
}
