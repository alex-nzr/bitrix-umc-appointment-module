<?php
ini_set('display_errors','Off');
ini_set('error_reporting', E_ERROR | E_PARSE );
error_reporting(E_ERROR | E_PARSE);

//header( 'Access-Control-Allow-Origin: *');
//header('Access-Control-Allow-Methods: *');
//header('Access-Control-Allow-Headers: *');
header('Content-Type: application/json');

use Firstbit\UclinicKursk\Context\Request;
use Firstbit\UclinicKursk\Service\OneC\Exchange;

$result = [];
try
{
    $autoloadFile = __DIR__ . '/../vendor/autoload.php';
    if (is_file($autoloadFile))
    {
        require_once $autoloadFile;
    }
    else
    {
        throw new Exception('Composer autoload file not found');
    }

    switch (Request::getAction())
    {
        case 'getAppointmentData':
            $result = Exchange::getAppointmentData();
            break;
        case 'createOrder':
            $orderData = Request::getPostJson();
            $orderRes = Exchange::sendOrder($orderData);
            if ($orderRes->isSuccess())
            {
                $result = array_merge([
                    'success' => true,
                ], $orderRes->getData());
            }
            else
            {
                throw new Exception(implode('; ', $orderRes->getErrorMessages()));
            }
            break;
        case 'sendEmail':
            //$orderData = Request::getPostJson();
            //todo send mail message with order info to client
            throw new Exception('Sending emails is not currently implemented');
        case 'checkSpam':
            /*$phone = $_REQUEST['phone'];
            if (empty($phone))
            {
                throw new Exception('Phone is empty');
            }

            $count = \Firstbit\UclinicKursk\Security\SpamProtector::getOrdersCountByPhoneToday($phone);
            if ($count >= 3)
            {
                throw new Exception('Daily orders limit has been exceeded');
            }
            else
            {
                \Firstbit\UclinicKursk\Security\SpamProtector::incrementOrdersCountByPhoneToday($phone);
                $count++;
            }

            $result = [
                'path' => \Firstbit\UclinicKursk\Security\SpamProtector::getPathToPhoneStoreFile(),
                'count' => $count,
            ];*/
            break;
        default:
            throw new Exception('Action is empty or unknown');
    }
}
catch(Throwable $e)
{
    $result = [
        'error' => $e->getMessage()
    ];
}
echo json_encode($result);
die();