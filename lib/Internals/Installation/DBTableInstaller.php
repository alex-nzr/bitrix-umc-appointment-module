<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.07.2022
 * ==================================================
*/
namespace ANZ\Appointment\Internals\Installation;

use ANZ\Appointment\Internals\Model\RecordTable;
use Bitrix\Main\Application;
use Bitrix\Main\Entity\Base;

/**
 * Class DBTableInstaller
 * @package ANZ\Appointment\Internals\Installation
 */
class DBTableInstaller
{
    private static array $dataClasses = [
        RecordTable::class
    ];

    /**
     * @throws \Exception
     */
    public static function install(): void
    {
        static::createDataTables(static::$dataClasses);
    }

    /**
     * @throws \Exception
     */
    public static function uninstall(): void
    {
        static::deleteDataTables(static::$dataClasses);
    }

    /**
     * @param array $dataClasses
     * @throws \Exception
     */
    private static function createDataTables(array $dataClasses): void
    {
        $connection = Application::getConnection();

        foreach ($dataClasses as $dataClass)
        {
            $dataTableName = Base::getInstance($dataClass)->getDBTableName();
            if(!$connection->isTableExists($dataTableName))
            {
                Base::getInstance($dataClass)->createDbTable();
            }
        }
    }

    /**
     * @param array $dataClasses
     * @throws \Exception
     */
    private static function deleteDataTables(array $dataClasses): void
    {
        $connection = Application::getConnection();

        foreach ($dataClasses as $dataClass)
        {
            $dataTableName = Base::getInstance($dataClass)->getDBTableName();
            if($connection->isTableExists($dataTableName))
            {
                $connection->dropTable($dataTableName);
            }
        }
    }
}