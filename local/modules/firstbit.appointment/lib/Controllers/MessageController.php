<?php
namespace FirstBit\Appointment\Controllers;

use Bitrix\Main\Engine\Action;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Result;
use FirstBit\Appointment\Services\Container;
use FirstBit\Appointment\Services\MailerService;
use Bitrix\Main\Engine\Controller;
use FirstBit\Appointment\Services\Operation\ConfirmOperation;
use FirstBit\Appointment\Services\SmsService;

class MessageController extends Controller
{
    private MailerService $mailerService;
    private SmsService $smsService;

    /**
     * OneCController constructor.
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function __construct()
    {
        parent::__construct();

        $container = Container::getInstance();
        $this->mailerService = $container->getMailerService();
        $this->smsService = $container->getSmsService();
    }

    public function sendConfirmCodeAction(string $phone = "", string $email = ""): Result
    {
        return ConfirmOperation::sendConfirmCode($this->mailerService, $this->smsService, $phone, $email);
    }

    public function verifyConfirmCodeAction(string $code): Result
    {
        return ConfirmOperation::verifyConfirmCode($code);
    }

    public function sendEmailNoteAction(string $params): Result
    {
        $arParams = json_decode($params, true);
        return $this->mailerService->sendEmailNote($arParams);
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