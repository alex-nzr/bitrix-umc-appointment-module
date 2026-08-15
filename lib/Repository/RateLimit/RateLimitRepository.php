<?php

namespace ANZ\Appointment\Repository\RateLimit;

use ANZ\Appointment\Model\RateLimitTable;
use ANZ\Appointment\Repository\EntityRepository;
use Bitrix\Main\Application;
use Bitrix\Main\ORM\Objectify\EntityObject;
use Bitrix\Main\SystemException;
use Throwable;

class RateLimitRepository extends EntityRepository implements RateLimitRepositoryInterface
{
    private const MAX_INSERT_ATTEMPTS = 3;

    /**
     * @throws SystemException
     */
    public function increment(string $key, int $expiresAt, int $now): int
    {
        $connection = Application::getConnection();
        $tableName = RateLimitTable::getTableName();
        $escapedKey = $connection->getSqlHelper()->forSql($key);

        for ($attempt = 1; $attempt <= self::MAX_INSERT_ATTEMPTS; $attempt++)
        {
            $connection->startTransaction();

            try
            {
                $record = $connection->query(
                    "SELECT REQUEST_COUNT, EXPIRES_AT FROM $tableName WHERE RATE_KEY = '$escapedKey' FOR UPDATE"
                )->fetch();

                if (!is_array($record))
                {
                    $connection->queryExecute(
                        "INSERT INTO $tableName (RATE_KEY, REQUEST_COUNT, EXPIRES_AT) VALUES ('$escapedKey', 1, $expiresAt)"
                    );
                    $connection->commitTransaction();

                    return 1;
                }

                $count = (int)$record['REQUEST_COUNT'];
                $recordExpiresAt = (int)$record['EXPIRES_AT'];
                if ($recordExpiresAt <= $now)
                {
                    $count = 0;
                    $recordExpiresAt = $expiresAt;
                }

                $count++;
                $connection->queryExecute(
                    "UPDATE $tableName SET REQUEST_COUNT = $count, EXPIRES_AT = $recordExpiresAt WHERE RATE_KEY = '$escapedKey'"
                );
                $connection->commitTransaction();

                return $count;
            }
            catch (Throwable $e)
            {
                $connection->rollbackTransaction();

                if ($attempt === self::MAX_INSERT_ATTEMPTS)
                {
                    throw new SystemException('Unable to update rate limit counter', 0, $e);
                }
            }
        }

        throw new SystemException('Unable to update rate limit counter');
    }

    protected function validate(EntityObject $entity): bool
    {
        return true;
    }
}
