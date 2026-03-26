<?php

namespace ANZ\Appointment\Tests\Unit\Repository\UMC;

use Bitrix\Main\ORM\Objectify\EntityObject;

class SpyAppointmentRepository extends FakeAppointmentRepository
{
    public array $deletedPrimaryKeys = [];
    public array $savedEntities = [];
    public ?EntityObject $entityByPrimary = null;

    public function save(EntityObject $entityObject): mixed
    {
        $this->savedEntities[] = $entityObject;
        return parent::save($entityObject);
    }

    public function getByPrimary(mixed $primary): ?EntityObject
    {
        return $this->entityByPrimary;
    }

    public function delete(mixed $primary): bool
    {
        $this->deletedPrimaryKeys[] = $primary;
        return true;
    }
}
