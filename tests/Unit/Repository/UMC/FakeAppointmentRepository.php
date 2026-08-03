<?php

namespace ANZ\Appointment\Tests\Unit\Repository\UMC;

use ANZ\Appointment\Repository\EntityRepository;
use Bitrix\Main\ORM\Objectify\EntityObject;

class FakeAppointmentRepository extends EntityRepository
{

    protected function validate(EntityObject $entity): bool
    {
        return $entity->has('ID');
    }

    public function save(EntityObject $entityObject): mixed
    {
        return $entityObject->getId();
    }

    public function getByPrimary(mixed $primary): ?EntityObject
    {
        return null;
    }

    public function delete(mixed $primary): bool
    {
        return true;
    }
}
