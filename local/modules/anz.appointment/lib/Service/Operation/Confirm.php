<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - Confirm.php
 * 10.07.2022 22:37
 * ==================================================
 */
namespace ANZ\Appointment\Service\Operation;

use ANZ\Appointment\Internals\Control\ServiceManager;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Result;
use Exception;
use ANZ\Appointment\Config\Constants;
use ANZ\Appointment\Service\Container;

/**
 * Class Confirm
 * @package ANZ\Appointment\Service\Operation
 */
class Confirm
{
    public function __construct(){}

    /**
     * @param string $phone
     * @param string $email
     * @return \Bitrix\Main\Result
     */
    public static function sendConfirmCode(string $phone, string $email): Result
    {
        try {
            $mailer = Container::getInstance()->getMailerService();
            $smsService = Container::getInstance()->getSmsService();

            $code = (string)rand(1000, 9999);
            $confirmWith = Option::get(
                ServiceManager::getModuleId(),
                Constants::OPTION_KEY_CONFIRM_MODE
            );
            $result = new Result();

            $session = Application::getInstance()->getSession();
            if ($session->has('confirm_code'))
            {
                $timeExpires = (int)$session->get('confirm_code_expires');
                if ($timeExpires > time()){
                    $result->addError(new Error(Loc::getMessage("ANZ_APPOINTMENT_CONFIRM_CODE_NOT_EXPIRED"), 425));
                    return $result;
                }
            }

            switch ($confirmWith){
                case Constants::CONFIRM_TYPE_PHONE:
                    $result = $smsService->sendConfirmCode($phone, $code);
                    break;
                case Constants::CONFIRM_TYPE_EMAIL:
                    $result = $mailer->sendConfirmCode($email, $code);
                    break;
                case Constants::CONFIRM_TYPE_NONE:
                default:
                    $result->addError(new Error(Loc::getMessage("ANZ_APPOINTMENT_CONFIRM_TYPE_ERROR"), 400));
                    break;
            }

            if ($result->isSuccess()){
                $timeExpires = time() + 60;
                $result->setData(['timeExpires' => $timeExpires]);
                $session->set('confirm_code', $code);
                $session->set('confirm_code_expires', $timeExpires);
            }

            return $result;
        }
        catch (Exception $e)
        {
            return (new Result)->addError(new Error($e->getMessage()));
        }
    }

    /**
     * @param string $code
     * @return \Bitrix\Main\Result
     */
    public static function verifyConfirmCode(string $code): Result
    {
        $result = new Result();
        $session = Application::getInstance()->getSession();
        if ($session->has('confirm_code'))
        {
            $timeExpires = (int)$session->get('confirm_code_expires');
            if ($timeExpires < time()){
                $result->addError(new Error(Loc::getMessage("ANZ_APPOINTMENT_CONFIRM_CODE_EXPIRED"), 406));
            }
            else
            {
                $correctCode = (string)$session->get('confirm_code');
                if ($correctCode === $code){
                    $result->setData(['success' => true]);
                }
                else
                {
                    $result->addError(new Error(Loc::getMessage("ANZ_APPOINTMENT_CONFIRM_CODE_INCORRECT"), 406));
                }
            }
        }
        else
        {
            $result->addError(new Error(Loc::getMessage("ANZ_APPOINTMENT_CONFIRM_CODE_EXPIRED"), 406));
        }
        return $result;
    }
}