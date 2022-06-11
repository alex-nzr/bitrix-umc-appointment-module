<?php
namespace FirstBit\Appointment\Controllers;

use Bitrix\Main\Engine\Action;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Exception;
use FirstBit\Appointment\Services\Container;
use Bitrix\Main\Engine\Controller;
use FirstBit\Appointment\Services\Operation\ConfirmOperation;

class MessageController extends Controller
{
    /**
     * MessageController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function sendConfirmCodeAction(string $phone = "", string $email = ""): Result
    {
        return ConfirmOperation::sendConfirmCode($phone, $email);
    }

    public function verifyConfirmCodeAction(string $code): Result
    {
        return ConfirmOperation::verifyConfirmCode($code);
    }

    /**
     * @param string $params
     * @return \Bitrix\Main\Result
     */
    public function sendEmailNoteAction(string $params): Result
    {
        try {
            $arParams = json_decode($params, true);
            $mailerService = Container::getInstance()->getMailerService();
            return $mailerService->sendEmailNote($arParams);
        }
        catch (Exception $e)
        {
            return (new Result)->addError(new Error($e->getMessage()));
        }
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