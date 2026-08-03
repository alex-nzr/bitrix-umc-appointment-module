<?php
namespace ANZ\Appointment\Core\Installation;

use ANZ\Appointment\Model\RecordTable;
use Bitrix\Main\Application;

class DBTableInstaller
{
    /** @var \Bitrix\Main\ORM\Data\DataManager[]|string[]  */
    private static array $dataClasses = [
        RecordTable::class
    ];

    /**
     * @throws \Exception
     */
    public static function install(): void
    {
        static::createDataTables();
    }

    /**
     * @throws \Exception
     */
    public static function uninstall(): void
    {
        static::deleteDataTables();
    }

    /**
     * @throws \Exception
     */
    private static function createDataTables(): void
    {
        $connection = Application::getConnection();
        foreach (static::$dataClasses as $dataClass)
        {
            $dataTableName = $dataClass::getEntity()->getDBTableName();
            if(!$connection->isTableExists($dataTableName))
            {
                $dataClass::getEntity()->createDbTable();
            }
        }
    }

    /**
     * @throws \Exception
     */
    private static function deleteDataTables(): void
    {
        $connection = Application::getConnection();

        foreach (static::$dataClasses as $dataClass)
        {
            $dataTableName = $dataClass::getEntity()->getDBTableName();
            if($connection->isTableExists($dataTableName))
            {
                $connection->dropTable($dataTableName);
            }
        }
    }
}
