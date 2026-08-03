<?php
namespace ANZ\Appointment\Service\Message;

use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Mail\Event;
use Bitrix\Main\Result;
use Bitrix\Main\Error;
use Exception;
use ANZ\Appointment\Config\Constants;

class Mailer
{
    public function sendConfirmCode(string $email, string $code): Result
    {
        return Event::send(array(
            "EVENT_NAME" => Constants::EMAIL_CONFIRM_EVENT_CODE,
            "LID" => Context::getCurrent()->getSite(),
            "C_FIELDS" => array(
                "EMAIL_TO" => $email,
                "CODE"     => $code,
            ),
        ));
    }

    public function sendEmailNote(array $params): Result
    {
        try
        {
            $name = htmlspecialcharsbx(($params["name"] ?? '') ." ". ($params["middleName"] ?? '') ." ". ($params["surname"] ?? ''));
            $emailTo = (string)($params["email"] ?? '');
            $phone = htmlspecialcharsbx((string)($params["phone"] ?? ''));
            $clinic = htmlspecialcharsbx((string)($params["clinicName"] ?? ''));
            $specialty = htmlspecialcharsbx((string)($params["specialty"] ?? ''));
            $service = htmlspecialcharsbx((string)($params["serviceName"] ?? ''));
            if (is_array($params["services"] ?? null))
            {
                $service = "";
                foreach ($params["services"] as $serviceItem)
                {
                    $service .= htmlspecialcharsbx((string)($serviceItem['name'] ?? '')) . "<br>";
                }
            }

            $doctor = htmlspecialcharsbx((string)($params["doctorName"] ?? ''));
            $dateTime = date("d.m.Y H:i", strtotime((string)($params["timeBegin"] ?? '')));
            $comment = htmlspecialcharsbx((string)($params["comment"] ?? ''));

            if (filter_var($emailTo, FILTER_VALIDATE_EMAIL))
            {
                $text = Loc::getMessage('ANZ_APPOINTMENT_MESSAGE_NOTE', [
                    "#CLINIC#"      => $clinic,
                    "#SPECIALTY#"   => $specialty,
                    "#SERVICE#"     => $service,
                    "#DOCTOR#"      => $doctor,
                    "#DATETIME#"    => $dateTime,
                    "#NAME#"        => $name,
                    "#PHONE#"       => $phone,
                    "#COMMENT#"     => $comment,
                ]);

                return Event::send([
                    "EVENT_NAME" => Constants::EMAIL_NOTE_EVENT_CODE,
                    "LID" => Context::getCurrent()->getSite(),
                    "C_FIELDS" => array(
                        "EMAIL_TO"  => $emailTo,
                        "TEXT"      => $text,
                    ),
                ]);
            }
            else {
                throw new Exception("EmailTo is empty");
            }
        }
        catch (Exception $e) {
            return (new Result)->addError(new Error($e->getMessage()));
        }
    }
}
