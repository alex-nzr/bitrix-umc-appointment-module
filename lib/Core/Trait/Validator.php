<?php

namespace ANZ\Appointment\Core\Trait;

use ANZ\Appointment\Core\Exception\ValidatorException;
use Bitrix\Main\UserPhoneAuthTable;

trait Validator
{
    /**
     * @throws \ANZ\Appointment\Core\Exception\ValidatorException
     */
    public function validateAndFormatPhone(string $phone): string
    {
        $phone = UserPhoneAuthTable::normalizePhoneNumber($phone);
        $phone = preg_replace(
            '/[^0-9]/',
            '',
            $phone);

        if(strlen($phone) > 10)
        {
            $phone = '+7' . substr($phone, -10);
        }

        $validationRes = UserPhoneAuthTable::validatePhoneNumber($phone);
        if ($validationRes !== true)
        {
            throw new ValidatorException(is_string($validationRes) ? $validationRes : 'Phone number is empty or invalid');
        }

        return  $phone;
    }

    /**
     * @throws \ANZ\Appointment\Core\Exception\ValidatorException
     */
    public function validateEmail(?string $email, bool $throwError = false): bool|string
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            if ($throwError)
            {
                throw new ValidatorException('Invalid email address');
            }
            return false;
        }
        return $email;
    }
}