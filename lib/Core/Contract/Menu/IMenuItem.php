<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 08.03.2025
 * ==================================================
*/
namespace ANZ\Appointment\Core\Contract\Menu;

interface IMenuItem
{
    public static function fromArray(array $data);
    public function isParent(): bool;
    public function getCompatibleData(): array;
}