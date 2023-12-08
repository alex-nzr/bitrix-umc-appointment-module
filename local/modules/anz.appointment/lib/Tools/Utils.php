<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - Utils.php
 * 10.07.2022 22:37
 * ==================================================
 */
namespace ANZ\Appointment\Tools;

use ANZ\BitUmc\SDK\Core\Operation\Result as SdkResult;
use Bitrix\Main\Error;
use Bitrix\Main\Result as BitrixResult;
use Bitrix\Main\SiteTable;

/**
 * Class Utils
 * @package ANZ\Appointment\Tools
 */
class Utils
{
    /**
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function getAllSiteIds(): array
    {
        $siteIds = [];
        $sites = SiteTable::query()->setSelect(['LID'])->exec()->fetchAll();
        if (is_array($sites) && count($sites) > 0){
            foreach ($sites as $site) {
                $siteIds[] = $site['LID'];
            }
        }
        return $siteIds;
    }

    /**
     * @param \ANZ\BitUmc\SDK\Core\Operation\Result $sdkResult
     * @return \Bitrix\Main\Result
     */
    public static function convertSdkResultToBitrixResult(SdkResult $sdkResult): BitrixResult
    {
        $bitrixResult = new BitrixResult();
        if ($sdkResult->isSuccess())
        {
            $bitrixResult->setData($sdkResult->getData());
        }
        else
        {
            foreach ($sdkResult->getErrorMessages() as $errorMessage)
            {
                $bitrixResult->addError(new Error($errorMessage));
            }
        }
        return $bitrixResult;
    }
}