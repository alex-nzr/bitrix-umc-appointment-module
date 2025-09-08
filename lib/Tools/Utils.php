<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.07.2022
 * ==================================================
*/
namespace ANZ\Appointment\Tools;

use Bitrix\Main\SiteTable;


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
}