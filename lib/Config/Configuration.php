<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.07.2022
 * ==================================================
*/
namespace ANZ\Appointment\Config;

use ANZ\Appointment\Core\Exception\ConfigurationException;
use ANZ\Appointment\Core\Trait\Singleton;
use ANZ\Appointment\Service\Container;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\NotImplementedException;
use CFile;
use DateTime as PhpDateTime;
use Exception;
use Throwable;

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
     * @throws \ANZ\Appointment\Core\Exception\ConfigurationException
     */
    public static function isInLocalHolder(): bool
    {
        $moduleHolder = Loader::LOCAL_HOLDER;
        $moduleDir = Application::getDocumentRoot() . "/$moduleHolder/modules/" . self::getModuleId();
        return is_dir($moduleDir);
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
    public function getDemoDataFilePath(bool $fullPath = false): string
    {
        return $this->getModuleLocation($fullPath) . '/storage/demoData.json';
    }

    public function isDemoModeOn(): bool
    {
        return (Option::get(self::$moduleId, Constants::OPTION_KEY_DEMO_MODE) === 'Y');
    }

    /**
     * @throws ConfigurationException
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
     * @throws ConfigurationException
     */
    public static function setModuleId(): void
    {
        $arr = explode(DIRECTORY_SEPARATOR, __FILE__);
        $i = array_search('modules', $arr);
        self::$moduleId = key_exists($i+1, $arr) ? $arr[$i+1] : '';
        if (empty(self::$moduleId))
        {
            throw new ConfigurationException('Can not determine module ID');
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

    public function getCustomBtnSelector(): string
    {
        return Option::get(self::$moduleId, Constants::OPTION_KEY_CUSTOM_BTN_SELECTOR);
    }

    public function isExchangeActive(): bool
    {
        return Option::get(self::$moduleId, Constants::OPTION_KEY_EXCHANGE_AGENT_ACTIVE) === 'Y';
    }

    public function getNextExchangeExecutionDate(): ?PhpDateTime
    {
        $val = Option::get(self::$moduleId, Constants::OPTION_KEY_EXCHANGE_NEXT_EXEC_DATE);
        try
        {
            return PhpDateTime::createFromFormat(self::DATE_FORMAT_FOR_OPTIONS, $val);
        }
        catch (Throwable)
        {
            return new PhpDateTime;
        }
    }

    /**
     * @throws \Exception
     */
    public function setExchangeActive(bool $value): void
    {
        Option::set(self::$moduleId, Constants::OPTION_KEY_EXCHANGE_AGENT_ACTIVE, $value ? 'Y' : 'N');
    }

    public function getSelectedClinics(): array
    {
        try {
            $val = Option::get(self::$moduleId, Constants::OPTION_KEY_EXCHANGE_CLINIC_SELECTOR);
            $decoded = json_decode($val, true);
        }
        catch (Throwable){
            $decoded = [];
        }
        return is_array($decoded) ? $decoded : [];
    }

    public function getDefaultClinic(): ?string
    {
        $val = Option::get(self::$moduleId, Constants::OPTION_KEY_EXCHANGE_CLINIC_DEFAULT);
        return $val ?: null;
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
        return Option::get(self::$moduleId, Constants::OPTION_KEY_EXCHANGE_CONFIRM_MODE, ConfirmationType::NONE->value);
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

    public function getCacheTtl(): int
    {
        $val = (int)Option::get(self::$moduleId, Constants::OPTION_KEY_EXCHANGE_CACHE_TTL);
        return $val > 0 ? $val : 3600 * 3;
    }

    /**
     * @throws \Exception
     */
    public function getOneCLogin(): string
    {
        return Option::get(self::$moduleId, Constants::OPTION_KEY_API_WS_LOGIN);
    }

    /**
     * @throws \Exception
     */
    public function getOneCPassword(): string
    {
        return Container::getInstance()->getEncryptService()->decrypt(
            Option::get(self::$moduleId, Constants::OPTION_KEY_API_WS_PASSWORD)
        );
    }

    /**
     * @throws \Exception
     */
    public function getOneCToken(): string
    {
        return Container::getInstance()->getEncryptService()->decrypt(
            Option::get(self::$moduleId, Constants::OPTION_KEY_API_HS_TOKEN)
        );
    }

    /**
     * @throws \Exception
     */
    public function getOneCApiUrl(): string
    {
        switch ($this->getExchangeMode())
        {
            case ExchangeMode::SOAP:
                return trim(Option::get(self::$moduleId, Constants::OPTION_KEY_API_WS_URL));
            case ExchangeMode::HTTP:
                throw new NotImplementedException('Http mode not implemented');
            default:
                throw new Exception('Unknown exchange mode');
        }
    }

    /**
     * @throws \Exception
     */
    public function getExchangeMode(): ?ExchangeMode
    {
        return ExchangeMode::tryFrom(Option::get(self::$moduleId, Constants::OPTION_KEY_EXCHANGE_MODE));
    }

    public function getDefaultAppointmentDuration(): int
    {
        return (int)Option::get(self::$moduleId, Constants::OPTION_KEY_EXCHANGE_DEFAULT_APPOINTMENT_DURATION);
    }

    public function getLogoFilePath(): ?string
    {
        $val = (int)Option::get(self::$moduleId, Constants::OPTION_KEY_LOGO);
        return $val > 0 ? CFile::GetPath($val) : '';
    }

    public function getTemplateColors(): array
    {
        return [
            Constants::OPTION_KEY_TEMPLATE_MAIN_COLOR => Option::get(
                self::$moduleId,
                Constants::OPTION_KEY_TEMPLATE_MAIN_COLOR,
                "#025ea1"
            ),
            Constants::OPTION_KEY_MAIN_BTN_TEXT_CLR => Option::get(
                self::$moduleId,
                Constants::OPTION_KEY_MAIN_BTN_TEXT_CLR,
                "#ffffff"
            ),
            Constants::OPTION_KEY_MAIN_BTN_BG => Option::get(
                self::$moduleId,
                Constants::OPTION_KEY_MAIN_BTN_BG,
                "#025ea1"
            ),
            Constants::OPTION_KEY_FIELD_BG => Option::get(
                self::$moduleId,
                Constants::OPTION_KEY_FIELD_BG,
                "#1B3257"
            ),
            Constants::OPTION_KEY_FORM_TEXT_CLR => Option::get(
                self::$moduleId,
                Constants::OPTION_KEY_FORM_TEXT_CLR,
                "#f5f5f5"
            ),
            Constants::OPTION_KEY_FORM_BTN_BG => Option::get(
                self::$moduleId,
                Constants::OPTION_KEY_FORM_BTN_BG,
                "#12b1e3"
            ),
            Constants::OPTION_KEY_FORM_BTN_TEXT_CLR => Option::get(
                self::$moduleId,
                Constants::OPTION_KEY_FORM_BTN_TEXT_CLR,
                "#ffffff"
            ),
        ];
    }

    public function isCustomTimeStepsEnabled(): bool
    {
        return Option::get(self::$moduleId, Constants::OPTION_KEY_USE_TIME_STEPS) === "Y";
    }

    public function getCustomTimeStepDurationMinutes(): bool
    {
        return (int)Option::get(self::$moduleId, Constants::OPTION_KEY_TIME_STEP_DURATION, 15);
    }

    public function isStrictCheckingRelationsEnabled(): bool
    {
        return Option::get(self::$moduleId, Constants::OPTION_KEY_STRICT_RELATIONS) === "Y";
    }

    public function isDoctorsWithoutDepartmentShowEnabled(): bool
    {
        return Option::get(self::$moduleId, Constants::OPTION_KEY_ALLOW_DOCTOR_WITHOUT_DPT) === "Y";
    }

    public function getPrivacyPageLink(): bool
    {
        return Option::get(self::$moduleId, Constants::OPTION_KEY_PRIVACY_PAGE, '#');
    }

    public function isEmailNotificationEnabled(): bool
    {
        return Option::get(self::$moduleId, Constants::OPTION_KEY_EMAIL_NOTE) === "Y";
    }

    public function isWaitListEnabled(): bool
    {
        return Option::get(self::$moduleId, Constants::OPTION_KEY_USE_WAIT_LIST) === "Y";
    }
}