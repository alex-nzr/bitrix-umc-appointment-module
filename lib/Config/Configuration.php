<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * Bit.Umc - Bitrix integration - Configuration.php
 * 05.03.2023 21:48
 * ==================================================
 */
namespace ANZ\Appointment\Config;

use ANZ\Appointment\Config\Options\System;
use ANZ\Appointment\Internals\ServiceManager;
use ANZ\BitUmc\SDK\Core\Trait\Singleton;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Web\Json;
use Throwable;

/**
 * @method static Configuration getInstance()
 */
final class Configuration
{
    use Singleton;

    private string $moduleId;

    protected function __construct()
    {
        $arr = explode(DIRECTORY_SEPARATOR, __FILE__);
        $i = array_search('modules', $arr);
        $this->moduleId = key_exists($i+1, $arr) ? $arr[$i+1] : '';
    }

    public function getLogFilePath(): string
    {
        return '/'.ServiceManager::getModuleParentDirectoryName().'/modules/'.$this->moduleId.'/logs/log.txt';
    }

    public function getDemoDataFilePath(): string
    {
        return '/'.ServiceManager::getModuleParentDirectoryName().'/modules/'.$this->moduleId.'/storage/demoData.json';
    }

    public function getCacheDir(): string
    {
        return '/'.ServiceManager::getModuleParentDirectoryName().'/modules/'.$this->moduleId.'/storage/cache/';
    }

    public function getAdminPagesDir(): string
    {
        return '/'.ServiceManager::getModuleParentDirectoryName().'/modules/'.$this->moduleId.'/admin/pages/';
    }

    public function isDemoModeOn(): bool
    {
        return (Option::get($this->moduleId, Constants::OPTION_KEY_DEMO_MODE) === 'Y');
    }

    public function isFtpModeOn(): bool
    {
        $request   = Context::getCurrent()->getRequest();
        $ignoreFtp = $request->getPost(Constants::REQUEST_KEY_IGNORE_FTP) === 'Y';
        $useFtp    = Option::get($this->moduleId, Constants::OPTION_KEY_USE_FTP_DATA) === 'Y';
        return ($useFtp && !$ignoreFtp);
    }

    public function getFtpDirectoriesMap(): array
    {
        $map = [];
        $mapJson = Option::get($this->moduleId, Constants::OPTION_KEY_FTP_DATA_MAP);

        if (is_string($mapJson) && !empty($mapJson))
        {
            try
            {
                $res = Json::decode($mapJson);
                if (is_array($res))
                {
                    foreach ($res as $uid => $path)
                    {
                        if (is_string($path) && (str_ends_with($path, '/')))
                        {
                            $path = substr($path, 0, strlen($path) - 1);
                        }
                        $map[$uid] = $path;
                    }
                }
            }
            catch(Throwable){}
        }
        return $map;
    }

    public function getModuleId(): string
    {
        return $this->moduleId;
    }

    public function isAutoIncludingOn(): bool
    {
        return Option::get($this->moduleId, Constants::OPTION_KEY_AUTO_INC) === "Y";
    }

    /**
     * @throws \Exception
     */
    public function needToUpdateUrlRewriteConditions(): bool
    {
        $conditionsHash = UrlRewriter::getUrlRewriteConditionsHash();
        $lastUpdatedConditionsHash = Option::get(
            $this->getModuleId(),
            System::OPTION_KEY_LAST_UPDATED_URL_CONDITIONS_HASH
        );

        return ($conditionsHash !== $lastUpdatedConditionsHash);
    }

    public function isCustomBtnEnabled(): bool
    {
        return Option::get($this->getModuleId(), Constants::OPTION_KEY_USE_CUSTOM_BTN) === 'Y';
    }

    public function getCustomBtnAttrId(): string
    {
        return Option::get($this->getModuleId(), Constants::OPTION_KEY_CUSTOM_BTN_ID);
    }
}