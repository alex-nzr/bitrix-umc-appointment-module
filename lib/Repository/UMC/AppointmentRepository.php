<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 05.09.2025
 * ==================================================
*/

namespace ANZ\Appointment\Repository\UMC;

use ANZ\Appointment\Core\Exception\ValidatorException;
use ANZ\Appointment\Model\RecordTable;
use ANZ\Appointment\Repository\EntityRepository;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ORM\Objectify\EntityObject;
use Bitrix\Main\SystemException;

class AppointmentRepository extends EntityRepository
{
    /**
     * @throws ValidatorException | ArgumentException | SystemException
     */
    protected function validate(EntityObject $entity): bool
    {
        if (!$entity->has(RecordTable::FIELD_NAME_EXTERNAL_ID) || empty($entity->get(RecordTable::FIELD_NAME_EXTERNAL_ID)))
        {
            throw new ValidatorException('Field ' . RecordTable::FIELD_NAME_EXTERNAL_ID . ' cannot be empty');
        }
        return true;
    }
}