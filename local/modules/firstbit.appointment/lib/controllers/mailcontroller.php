<?php
namespace FirstBit\Appointment\Controllers;

use FirstBit\Appointment\Services\MailerService;
use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Error;
use FirstBit\Appointment\Utils\Utils;

class MailController extends Controller
{
    private MailerService $mailer;

    /**
     * OneCController constructor.
     * @throws \Bitrix\Main\ObjectNotFoundException
     */
    public function __construct()
    {
        parent::__construct();

        $serviceLocator = ServiceLocator::getInstance();

        if ($serviceLocator->has('appointment.MailerService'))
        {
            $this->mailer = $serviceLocator->get('appointment.MailerService');
        }
    }

    public function sendEmailNoteAction(string $params): ?array
    {
        $arParams = json_decode($params, true);
        $response = $this->mailer->sendEmail($arParams);
        if ($response['error']){
            $this->addError(new Error($response['error']));
            return null;
        }
        return $response;
    }

    public function configureActions(): array
    {
        return [
            'sendEmailNote'     => [ 'prefilters' => [], 'postfilters' => [] ],
        ];
    }
}