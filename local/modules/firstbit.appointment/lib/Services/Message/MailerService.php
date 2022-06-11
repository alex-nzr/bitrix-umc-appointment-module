<?php
namespace FirstBit\Appointment\Services\Message;

use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Mail\Event;
use Bitrix\Main\Result;
use Bitrix\Main\Error;
use Exception;
use FirstBit\Appointment\Config\Constants;

class MailerService{

    public function __construct(){}

    /**
     * @param string $email
     * @param string $code
     * @return \Bitrix\Main\Result
     */
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

    /**
     * @param array $params
     * @return \Bitrix\Main\Result
     */
    public function sendEmailNote(array $params): Result
    {
        try
        {
            $name = htmlspecialchars($params["name"] ." ". $params["middleName"] ." ". $params["surname"]);
            $emailTo = htmlspecialchars($params["email"]);
            $phone = htmlspecialchars($params["phone"]);
            $clinic = htmlspecialchars($params["clinicName"]);
            $specialty = htmlspecialchars($params["specialty"]);
            $service = htmlspecialchars($params["serviceName"]);
            if (is_array($params["services"]))
            {
                $service = "";
                foreach ($params["services"] as $serviceItem) {
                    $service .= $serviceItem['name']."<br>";
                }
            }

            $doctor = htmlspecialchars($params["doctorName"]);
            $dateTime = date("d.m.Y H:i", strtotime($params["timeBegin"]));
            $comment = htmlspecialchars($params["comment"]);

            if (!empty($emailTo))
            {
                $text = Loc::getMessage('FIRSTBIT_APPOINTMENT_MESSAGE_NOTE', [
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