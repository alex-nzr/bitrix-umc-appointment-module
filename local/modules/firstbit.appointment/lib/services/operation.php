<?php
namespace FirstBit\Appointment\Services;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Result;
use FirstBit\Appointment\Config\Constants;

class Operation
{
    public function __construct(){}

    public static function sendConfirmCode(MailerService $mailer, SmsService $smsService, string $phone, string $email): Result
    {
        $code = (string)rand(1000, 9999);
        $confirmWith = Option::get(Constants::APPOINTMENT_MODULE_ID, 'appointment_settings_confirm_with');
        $result = new Result();

        $session = Application::getInstance()->getSession();
        if ($session->has('confirm_code'))
        {
            $timeExpires = (int)$session->get('confirm_code_expires');
            if ($timeExpires > time()){
                $result->addError(new Error(Loc::getMessage("FIRSTBIT_APPOINTMENT_CONFIRM_CODE_NOT_EXPIRED")));
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
                $result->addError(new Error(Loc::getMessage("FIRSTBIT_APPOINTMENT_CONFIRM_TYPE_ERROR")));
                break;
        }

        if ($result->isSuccess()){
            $session->set('confirm_code', $code);
            $session->set('confirm_code_expires', time() + 60);
        }

        return $result;
    }


}