<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 05.09.2025
 * ==================================================
*/
namespace ANZ\Appointment\Repository;

use Bitrix\Main\ORM\Objectify\EntityObject;

interface RepositoryInterface
{
    public function getByPrimary(mixed $primary): ?EntityObject;
    public function save(EntityObject $entityObject): mixed;
    public function delete(mixed $primary): bool;
}