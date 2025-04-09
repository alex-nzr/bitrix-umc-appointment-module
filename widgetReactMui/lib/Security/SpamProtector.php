<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2024
 * ==================================================
 * uclinic.kursk - SpamProtector.php
 * 02.10.2024 19:35
 * ==================================================
 */
namespace Firstbit\UclinicKursk\Security;

use DateTime;

/**
 * @class SpamProtector
 * @package Firstbit\UclinicKursk\Security
 */
class SpamProtector
{
    /**
     * @param string $phone
     * @return int
     */
    public static function getOrdersCountByPhoneToday(string $phone): int
    {
        $fileName = static::getPathToPhoneStoreFile();
        if (is_file($fileName))
        {
            $formattedDate = (new DateTime())->format('d.m.Y');
            $fileData = json_decode(file_get_contents($fileName), true);
            if (is_array($fileData) && key_exists($formattedDate, $fileData))
            {
                if (is_array($fileData[$formattedDate]) && key_exists($phone, $fileData[$formattedDate]))
                {
                    return (int)$fileData[$formattedDate][$phone];
                }
            }
        }

        return 0;
    }

    /**
     * @param string $phone
     * @return void
     */
    public static function incrementOrdersCountByPhoneToday(string $phone): void
    {
        $formattedDate = (new DateTime())->format('d.m.Y');
        $fileName = static::getPathToPhoneStoreFile();
        $data = [
            $formattedDate => []
        ];
        if (is_file($fileName))
        {
            $fileData = json_decode(file_get_contents($fileName), true);
            if (is_array($fileData) && key_exists($formattedDate, $fileData))
            {
                $data = $fileData;
            }
        }

        if (key_exists($phone, $data[$formattedDate]))
        {
            $data[$formattedDate][$phone] = (int)$data[$formattedDate][$phone] + 1;
        }
        else
        {
            $data[$formattedDate][$phone] = 1;
        }

        file_put_contents($fileName, json_encode($data));
    }

    /**
     * @return string
     */
    public static function getPathToPhoneStoreFile(): string
    {
        return __DIR__ . '/../../store/ordersByPhone.json';
    }
}