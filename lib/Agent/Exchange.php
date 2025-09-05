<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 09.03.2025
 * ==================================================
*/
namespace ANZ\Appointment\Agent;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Service\Container;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\IO\Directory;
use DateTime as PhpDateTime;
use Exception;
use Throwable;

class Exchange
{
    /**
     * @throws \Throwable
     */
    public static function loadData(bool $manuallyExecution = false, bool $throwError = false): string
    {
        $isActive = null;
        try
        {
            $isActive = Configuration::getInstance()->isExchangeActive();
            if ($manuallyExecution || $isActive)
            {
                $dateOfNext = Configuration::getInstance()->getNextExchangeExecutionDate();
                $currentDate = new PhpDateTime();
                if ($manuallyExecution ||
                    ($dateOfNext instanceof PhpDateTime && ($currentDate->getTimestamp() >= $dateOfNext->getTimestamp()))
                )
                {
                    Configuration::getInstance()->setExchangeActive(false);

                    Container::getInstance()->getExchangeManager()->renewCacheData($manuallyExecution);

                    Configuration::getInstance()->setLastExchangeExecutionDate(new PhpDateTime);

                    Configuration::getInstance()->setNextExchangeExecutionDate(
                        (new PhpDateTime())->modify(
                            '+'.Configuration::getInstance()->getExchangeExecutionInterval().' minute'
                        )
                    );

                    Configuration::getInstance()->setExchangeActive(true);
                }
            }
        }
        catch (Throwable $e)
        {
            $logFilePath = Configuration::getInstance()->getExchangeLogFilePath();
            Debug::writeToFile(
                [
                    'MESSAGE' => $e->getMessage(),
                    'TRACE' => $e->getTrace()
                ],
                __METHOD__ . ' ' . date('Y-m-d H:i:s'),
                $logFilePath
            );

            if ($throwError)
            {
                throw $e;
            }

            if ($isActive)
            {
                Configuration::getInstance()->setExchangeActive(true);
            }
        }

        return __METHOD__ . '();';
    }

    /**
     * @throws \Exception
     */
    public static function cleanLogFiles(): string
    {
        try
        {
            $periodDays = Configuration::getInstance()->getLogsTTL();
            $path = Configuration::getInstance()->getLogFileDir(true);
            if (Directory::isDirectoryExists($path))
            {
                if ($dir = opendir($path))
                {
                    while ($item = readdir($dir))
                    {
                        if ($item === '.keepme')
                        {
                            continue;
                        }

                        if (is_file($path . DIRECTORY_SEPARATOR . $item))
                        {
                            if (filemtime($path . DIRECTORY_SEPARATOR . $item) < (time() - $periodDays * 86400))
                            {
                                try
                                {
                                    unlink($path . DIRECTORY_SEPARATOR . $item);
                                }
                                catch(Exception)
                                {
                                    continue;
                                }
                            }
                        }
                    }
                    closedir($dir);
                }
            }
        }
        catch (Throwable $e)
        {
            Debug::writeToFile(
                [
                    'MESSAGE' => $e->getMessage(),
                    //'TRACE' => $e->getTrace()
                ],
                __METHOD__ . ' ' . date('Y-m-d H:i:s'),
                Configuration::getInstance()->getCommonLogFilePath()
            );
        }

        return __METHOD__ . "();";
    }

    /**
     * @throws \Exception
     */
    public static function cleanModuleCache(): string
    {
        try
        {
            Container::getInstance()->getUmcIntegrationCacheProvider()->cleanAll();
        }
        catch (Throwable $e)
        {
            Debug::writeToFile(
                [
                    'MESSAGE' => $e->getMessage(),
                    //'TRACE' => $e->getTrace()
                ],
                __METHOD__ . ' ' . date('Y-m-d H:i:s'),
                Configuration::getInstance()->getCommonLogFilePath()
            );
        }

        return __METHOD__ . "();";
    }
}