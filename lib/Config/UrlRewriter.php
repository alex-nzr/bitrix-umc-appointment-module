<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 08.03.2025
 * ==================================================
*/
namespace ANZ\Appointment\Config;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\Config\Option;

class UrlRewriter extends \Bitrix\Main\UrlRewriter
{
    public static function getUrlRewriteConditions(): array
    {
        return [
            [
                'CONDITION' => '#^/bitrix/admin/anz.app.list.page.php#',
                'RULE' => '',
                'ID' => null,
                'PATH' => Configuration::getInstance()->getAdminPagesDir().'anz.app.list.page.php',
                'SITE_ID' => 'um'
            ],
            [
                'CONDITION' => '#^/bitrix/admin/anz.app.settings.page.php#',
                'RULE' => '',
                'ID' => null,
                'PATH' => Configuration::getInstance()->getAdminPagesDir().'anz.app.settings.page.php',
                'SITE_ID' => 'um'
            ],

            //USE DEL=Y to delete condition on next hit
            //example:
            /*[
                'CONDITION' => '#^/bitrix/admin/to-delete.php#',
                'DEL' => 'Y'
            ]*/
        ];
    }

    public static function getUrlRewriteConditionsHash(): string
    {
        return hash('sha512', json_encode(static::getUrlRewriteConditions()));
    }

    /**
     * @throws \Exception
     */
    public static function updateRules(): void
    {
        foreach (static::getUrlRewriteConditions() as $urlRewriteItem)
        {
            $siteId = $urlRewriteItem['SITE_ID'];
            $condition = $urlRewriteItem['CONDITION'];
            if (empty($siteId))
            {
                $siteId = 's1';
            }

            if (empty($condition))
            {
                throw new ArgumentNullException('CONDITION');
            }

            $arResult = static::getList($siteId, ['CONDITION' => $condition]);
            if (!empty($arResult))
            {
                if (key_exists('DEL', $urlRewriteItem) && $urlRewriteItem['DEL'] === 'Y')
                {
                    static::delete(
                        $siteId,
                        ['CONDITION' => $condition]
                    );
                }
                else
                {
                    static::update(
                        $siteId,
                        ['CONDITION' => $condition],
                        [
                            'CONDITION' => $condition,
                            'ID' => $urlRewriteItem['ID'],
                            'PATH' => $urlRewriteItem['PATH'],
                            'RULE' => $urlRewriteItem['RULE']
                        ]
                    );
                }
            }
            else
            {
                static::add(
                    $siteId,
                    [
                        'CONDITION' => $condition,
                        'ID' => $urlRewriteItem['ID'],
                        'PATH' => $urlRewriteItem['PATH'],
                        'RULE' => $urlRewriteItem['RULE']
                    ]
                );
            }
        }

        Option::set(
            Configuration::getInstance()->getModuleId(),
            Options\System::OPTION_KEY_LAST_UPDATED_URL_CONDITIONS_HASH,
            static::getUrlRewriteConditionsHash()
        );
    }
}