<?php
namespace FirstBit\Appointment\Services;

use Bitrix\Main\Mail\Event;

class MailerService{

    public function __construct(){ }

    /**
     * @param array $params
     * @return array
     */
    public function sendEmail(array $params): array
    {
        if (is_array($params)){
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
                $text = "
                    Вы успешно записались на приём
                    Клиника: $clinic
                    Специализация: $specialty
                    Услуги: $service
                    Врач: $doctor
                    Дата/время: $dateTime
                    ФИО: $name
                    Номер телефона: $phone
                    Комментарий: $comment
                ";
                Event::send(array(
                    "EVENT_NAME" => "FEEDBACK_FORM",
                    'MESSAGE_ID' => 7,
                    "LID" => SITE_ID,
                    "C_FIELDS" => array(
                        "AUTHOR" => $name,
                        "AUTHOR_EMAIL" => $emailTo,
                        'EMAIL_TO' => $emailTo,
                        "TEXT" => $text,
                    ),
                ));

                return ['success' => true];
            }
            else {
                return ['error' => "EmailTo is empty"];
            }
        }
        else {
            return ['error' => "Invalid type of params. Array expected, but ". gettype($params). " given"];
        }
    }
}