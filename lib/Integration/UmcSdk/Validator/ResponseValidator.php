<?php
/*
 * ==================================================
 * This file is part of project bitrix.firstbit.umc
 * 22.08.2025
 * ==================================================
*/

namespace ANZ\Appointment\Integration\UmcSdk\Validator;

class ResponseValidator
{
    public function validateClinic(mixed $data): bool
    {
        if (is_array($data))
        {
            return key_exists('uid', $data) && !empty($data['uid'])
                    && key_exists('name', $data) && !empty($data['name']);
        }
        return false;
    }

    public function validateEmployee(mixed $data): bool
    {
        if (is_array($data))
        {
            return key_exists('uid', $data) && !empty($data['uid'])
                && key_exists('name', $data) && !empty($data['name']);
        }
        return false;
    }

    public function validateService(mixed $data): bool
    {
        if (is_array($data))
        {
            return key_exists('uid', $data) && !empty($data['uid'])
                && key_exists('name', $data) && !empty($data['name']);
        }
        return false;
    }
}