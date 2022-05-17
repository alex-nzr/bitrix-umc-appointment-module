<?php
namespace FirstBit\Appointment\Services\Operation;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Result;
use FirstBit\Appointment\Config\Constants;
use FirstBit\Appointment\Services\MailerService;
use FirstBit\Appointment\Services\SmsService;

class ConfirmOperation
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
                $result->addError(new Error(Loc::getMessage("FIRSTBIT_APPOINTMENT_CONFIRM_CODE_NOT_EXPIRED"), 425));
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
                $result->addError(new Error(Loc::getMessage("FIRSTBIT_APPOINTMENT_CONFIRM_TYPE_ERROR"), 400));
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

    public static function verifyConfirmCode(string $code): Result
    {
        $result = new Result();
        $session = Application::getInstance()->getSession();
        if ($session->has('confirm_code'))
        {
            $timeExpires = (int)$session->get('confirm_code_expires');
            if ($timeExpires < time()){
                $result->addError(new Error(Loc::getMessage("FIRSTBIT_APPOINTMENT_CONFIRM_CODE_EXPIRED"), 406));
            }
            else
            {
                $correctCode = (string)$session->get('confirm_code');
                if ($correctCode === $code){
                    $result->setData(['success' => true]);
                }
                else
                {
                    $result->addError(new Error(Loc::getMessage("FIRSTBIT_APPOINTMENT_CONFIRM_CODE_INCORRECT"), 406));
                }
            }
        }
        else
        {
            $result->addError(new Error(Loc::getMessage("FIRSTBIT_APPOINTMENT_CONFIRM_CODE_EXPIRED"), 406));
        }
        return $result;
    }
}