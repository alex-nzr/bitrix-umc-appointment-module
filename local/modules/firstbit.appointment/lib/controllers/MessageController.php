<?php
namespace FirstBit\Appointment\Controllers;

use Bitrix\Main\Engine\Action;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Result;
use FirstBit\Appointment\Config\Constants;
use FirstBit\Appointment\Services\MailerService;
use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Engine\Controller;
use FirstBit\Appointment\Services\Operation\ConfirmOperation;
use FirstBit\Appointment\Services\SmsService;

class MessageController extends Controller
{
    private string $mailerServiceId;
    private string $smsServiceId;
    private MailerService $mailer;
    private SmsService $smsService;

    /**
     * OneCController constructor.
     * @throws \Bitrix\Main\ObjectNotFoundException
     */
    public function __construct()
    {
        parent::__construct();

        $this->mailerServiceId = Constants::MAILER_SERVICE_ID;
        $this->smsServiceId    = Constants::SMS_SERVICE_ID;

        $serviceLocator = ServiceLocator::getInstance();

        if ($serviceLocator->has($this->mailerServiceId))
        {
            $this->mailer = $serviceLocator->get($this->mailerServiceId);
        }

        if ($serviceLocator->has($this->smsServiceId))
        {
            $this->smsService = $serviceLocator->get($this->smsServiceId);
        }
    }

    public function sendConfirmCodeAction(string $phone = "", string $email = ""): Result
    {
        return ConfirmOperation::sendConfirmCode($this->mailer, $this->smsService, $phone, $email);
    }

    public function verifyConfirmCodeAction(string $code): Result
    {
        return ConfirmOperation::verifyConfirmCode($code);
    }

    public function sendEmailNoteAction(string $params): Result
    {
        $arParams = json_decode($params, true);
        return $this->mailer->sendEmailNote($arParams);
    }

    protected function getDefaultPreFilters(): array
    {
        return [
            new HttpMethod([HttpMethod::METHOD_POST]),
            new Csrf(),
        ];
    }

    protected function processAfterAction(Action $action, $result)
    {
        if ($result instanceof Result)
        {
            if ($result->isSuccess())
            {
                return $result->getData();
            }
            else
            {
                $errors = $result->getErrors();
                if (is_array($errors))
                {
                    foreach ($errors as $error)
                    {
                        $this->addError($error);
                    }
                }
                return null;
            }
        }
        return null;
    }
}