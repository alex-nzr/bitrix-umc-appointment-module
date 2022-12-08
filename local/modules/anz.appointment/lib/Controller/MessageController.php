<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - MessageController.php
 * 10.07.2022 22:37
 * ==================================================
 */
namespace ANZ\Appointment\Controller;

use Bitrix\Main\Engine\Action;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Exception;
use ANZ\Appointment\Service\Container;
use Bitrix\Main\Engine\Controller;
use ANZ\Appointment\Service\Operation\Confirm;

/**
 * Class MessageController
 * @package ANZ\Appointment\Controller
 */
class MessageController extends Controller
{
    /**
     * MessageController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param string $phone
     * @param string $email
     * @return \Bitrix\Main\Result
     */
    public function sendConfirmCodeAction(string $phone = "", string $email = ""): Result
    {
        return Confirm::sendConfirmCode($phone, $email);
    }

    /**
     * @param string $code
     * @return \Bitrix\Main\Result
     */
    public function verifyConfirmCodeAction(string $code): Result
    {
        return Confirm::verifyConfirmCode($code);
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

    /**
     * @return array
     */
    protected function getDefaultPreFilters(): array
    {
        return [
            new HttpMethod([HttpMethod::METHOD_POST]),
            new Csrf(),
        ];
    }

    /**
     * @param \Bitrix\Main\Engine\Action $action
     * @param $result
     * @return array|\Bitrix\Main\HttpResponse|mixed|void|null
     */
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