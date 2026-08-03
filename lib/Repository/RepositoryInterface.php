<?php
namespace ANZ\Appointment\Repository;

use Bitrix\Main\ORM\Objectify\EntityObject;

interface RepositoryInterface
{
    public function getByPrimary(mixed $primary): ?EntityObject;
    public function save(EntityObject $entityObject): mixed;
    public function delete(mixed $primary): bool;
}
