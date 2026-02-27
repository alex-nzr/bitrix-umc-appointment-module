<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.07.2022
 * ==================================================
*/
namespace ANZ\Appointment\Service\Security;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Config\ConfirmationType;
use ANZ\Appointment\Service\Container;
use Bitrix\Main\Application;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Result;
use Exception;

class Confirmation
{
    public function sendConfirmCode(string $phone, string $email): Result
    {
        try
        {
            $mailer = Container::getInstance()->getMailerService();
            $smsService = Container::getInstance()->getSmsService();

            $code = (string)rand(1000, 9999);
            $confirmWith = Configuration::getInstance()->getExchangeConfirmMode();
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
                case ConfirmationType::PHONE->value:
                    $result = $smsService->sendConfirmCode($phone, $code);
                    break;
                case ConfirmationType::EMAIL->value:
                    $result = $mailer->sendConfirmCode($email, $code);
                    break;
                case ConfirmationType::NONE->value:
                    break;
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

    public function verifyConfirmCode(string $code): Result
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