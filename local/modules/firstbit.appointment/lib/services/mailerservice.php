<?php
namespace AlexNzr\BitUmcIntegration\Service;

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
                $headers = [
                    'from' => 'no-reply@'.$_SERVER['HTTP_HOST'],
                    'MIME-Version' => '1.0',
                    'Content-type' => 'text/html;charset=utf-8',
                ];

                $subject = 'Запись на приём';
                $html = '
                    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
                    <html lang="ru">
                        <head>
                            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                            <title>'.$subject.'</title>
                        </head>
                        <body>
                            <table>
                                <thead>
                                    <tr>
                                        <th colspan="2">Вы успешно записались на приём</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td>Клиника:</td>       <td>'. $clinic .'</td></tr>
                                    <tr><td>Специализация:</td> <td>'. $specialty .'</td></tr>
                                    <tr><td>Услуги:</td>        <td>'. $service .'</td></tr>
                                    <tr><td>Врач:</td>          <td>'. $doctor .'</td></tr>
                                    <tr><td>Дата/время:</td>    <td>'. $dateTime .'</td></tr>
                                    <tr><td>ФИО:</td>           <td>'. $name .'</td></tr>
                                    <tr><td>Номер телефона:</td><td>'. $phone .'</td></tr>
                                    <tr><td>Комментарий:</td>   <td>'. $comment .'</td></tr>
                                </tbody>
                            </table>
                        </body>
                    </html>';

                return ['success' => mail($emailTo, $subject, $html, $headers)];
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