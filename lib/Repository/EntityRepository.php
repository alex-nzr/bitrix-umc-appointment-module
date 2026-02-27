<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 05.09.2025
 * ==================================================
*/
namespace ANZ\Appointment\Repository;

use ANZ\Appointment\Core\Exception\EntityRepositoryException;
use Bitrix\Main\ORM\Entity;
use Bitrix\Main\ORM\Objectify\EntityObject;
use Exception;
use Throwable;

abstract class EntityRepository implements RepositoryInterface
{
    public function __construct(protected Entity $entity)
    {
    }

    /**
     * @throws EntityRepositoryException
     */
    public function save(EntityObject $entityObject): mixed
    {
        try
        {
            if ($this->validate($entityObject))
            {
                $result = $entityObject->save();
                if (!$result->isSuccess())
                {
                    throw new Exception(implode("\n", $result->getErrorMessages()));
                }
            }
            return $entityObject->getId();
        }
        catch (Throwable $e)
        {
            throw new EntityRepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws EntityRepositoryException
     */
    public function delete(mixed $primary): bool
    {
        try
        {
            $result = $this->entity->getDataClass()::delete($primary);
            if (!$result->isSuccess())
            {
                throw new EntityRepositoryException(implode("\n", $result->getErrorMessages()));
            }
            return true;
        }
        catch (Throwable $e)
        {
            throw new EntityRepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws EntityRepositoryException
     */
    public function getByPrimary(mixed $primary): ?EntityObject
    {
        try
        {
            return $this->entity->getDataClass()::getByPrimary($primary, ['select' => ['*']])->fetchObject();
        }
        catch (Throwable $e)
        {
            throw new EntityRepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }

    abstract protected function validate(EntityObject $entity): bool;
}