<?php
namespace ANZ\Appointment\Core\Contract\Menu;

interface IMenuItem
{
    public static function fromArray(array $data);
    public function isParent(): bool;
    public function getCompatibleData(): array;
}
