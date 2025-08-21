<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.07.2022
 * ==================================================
*/
namespace ANZ\Appointment\Config;

use ANZ\BitUmc\SDK\Core\Trait\Singleton;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use DateTime as PhpDateTime;
use Exception;

/**
 * @method static Configuration getInstance()
 */
final class Configuration
{
    use Singleton;

    const DATE_FORMAT_FOR_OPTIONS = 'Y-m-d\TH:i';

    protected static string $moduleId = '';

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        self::setModuleId();
    }

    /**
     * @throws \Exception
     */
    public static function getModuleLocation(bool $fullPath = true): string
    {
        $moduleHolder = Loader::LOCAL_HOLDER;
        $moduleDir = Application::getDocumentRoot() . "/$moduleHolder/modules/" . self::getModuleId();
        if (!is_dir($moduleDir))
        {
            $moduleHolder = Loader::BITRIX_HOLDER;
            $moduleDir = Application::getDocumentRoot() . "/$moduleHolder/modules/" . self::getModuleId();
            if (!is_dir($moduleDir))
            {
                throw new Exception('Can not determine module directory');
            }
        }

        if ($fullPath)
        {
            return $moduleDir;
        }
        else
        {
            return str_replace(Application::getDocumentRoot(), '', $moduleDir);
        }
    }

    /**
     * @throws \Exception
     */
    public function getLogFileDir(bool $fullPath = false): string
    {
        return $this::getModuleLocation($fullPath) . '/logs';
    }

    /**
     * @throws \Exception
     */
    public function getCommonLogFilePath(): string
    {
        return $this->getLogFileDir() . '/' . date('Y-m-d') . '_common.log.txt';
    }

    /**
     * @throws \Exception
     */
    public function getExchangeLogFilePath(): string
    {
        return $this->getLogFileDir() . '/' . date('Y-m-d') . '_exchange.log.txt';
    }

    /**
     * @throws \Exception
     */
    public function getDemoDataFilePath(): string
    {
        return $this->getModuleLocation(false) . '/storage/demoData.json';
    }

    public function isDemoModeOn(): bool
    {
        return (Option::get(self::$moduleId, Constants::OPTION_KEY_DEMO_MODE) === 'Y');
    }

    /**
     * @throws \Exception
     */
    public static function getModuleId(): string
    {
        if (empty(self::$moduleId))
        {
            self::setModuleId();
        }
        return self::$moduleId;
    }

    /**
     * @throws \Exception
     */
    public static function setModuleId(): void
    {
        $arr = explode(DIRECTORY_SEPARATOR, __FILE__);
        $i = array_search('modules', $arr);
        self::$moduleId = key_exists($i+1, $arr) ? $arr[$i+1] : '';
        if (empty(self::$moduleId))
        {
            throw new Exception('Can not determine module ID');
        }
    }

    public function isAutoIncludingOn(): bool
    {
        return Option::get(self::$moduleId, Constants::OPTION_KEY_AUTO_INC) === "Y";
    }

    public function isCustomBtnEnabled(): bool
    {
        return Option::get(self::$moduleId, Constants::OPTION_KEY_USE_CUSTOM_BTN) === 'Y';
    }

    public function getCustomBtnAttrId(): string
    {
        return Option::get(self::$moduleId, Constants::OPTION_KEY_CUSTOM_BTN_ID);
    }

    public function isExchangeActive(): bool
    {
        return Option::get(self::$moduleId, Constants::OPTION_KEY_EXCHANGE_AGENT_ACTIVE) === 'Y';
    }

    /**
     * @throws \Exception
     */
    public function getNextExchangeExecutionDate(): ?PhpDateTime
    {
        $val = Option::get(self::$moduleId, Constants::OPTION_KEY_EXCHANGE_NEXT_EXEC_DATE);
        if (strlen($val) === strlen(self::DATE_FORMAT_FOR_OPTIONS))
        {
            return PhpDateTime::createFromFormat(self::DATE_FORMAT_FOR_OPTIONS, $val);
        }
        return new PhpDateTime;
    }

    /**
     * @throws \Exception
     */
    public function setExchangeActive(bool $value): void
    {
        Option::set(self::$moduleId, Constants::OPTION_KEY_EXCHANGE_AGENT_ACTIVE, $value ? 'Y' : 'N');
    }

    public function isServicesEnabled(): bool
    {
        return Option::get(self::$moduleId, Constants::OPTION_KEY_EXCHANGE_USE_SERVICES) === 'Y';
    }

    /**
     * @throws \Exception
     */
    public function setLastExchangeExecutionDate(PhpDateTime $value): void
    {
        Option::set(self::$moduleId, Constants::OPTION_KEY_EXCHANGE_LAST_EXEC_DATE, $value->format(self::DATE_FORMAT_FOR_OPTIONS));
    }

    /**
     * @throws \Exception
     */
    public function setNextExchangeExecutionDate(PhpDateTime $value): void
    {
        Option::set(self::$moduleId, Constants::OPTION_KEY_EXCHANGE_NEXT_EXEC_DATE, $value->format(self::DATE_FORMAT_FOR_OPTIONS));
    }

    public function getExchangeExecutionInterval(): int
    {
        $val = (int)Option::get(self::$moduleId, Constants::OPTION_KEY_EXCHANGE_EXEC_INTERVAL);
        return $val > 0 ? $val : 15;
    }

    public function getExchangeSchedulePeriod(): int
    {
        $val = (int)Option::get(self::$moduleId, Constants::OPTION_KEY_EXCHANGE_SCHEDULE_PERIOD);
        return $val > 0 ? $val : 14;
    }

    public function getExchangeConfirmMode(): string
    {
        return Option::get(self::$moduleId, Constants::OPTION_KEY_EXCHANGE_CONFIRM_MODE, Constants::CONFIRM_TYPE_NONE);
    }

    public function getLogsTTL(): int
    {
        $val = (int)Option::get(self::$moduleId, Constants::OPTION_KEY_DEBUG_LOGS_TTL);
        return $val > 0 ? $val : 30;
    }

    public function getJsExtensionName(): string
    {
        $customExt = Option::get(self::$moduleId, Constants::OPTION_KEY_CUSTOM_JS_EXTENSION);
        if (strlen($customExt) > 0)
        {
            return $customExt;
        }

        return Option::get(self::$moduleId, Constants::OPTION_KEY_JS_EXTENSION);
    }
}