<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - Writer.php
 * 10.07.2022 22:37
 * ==================================================
 */
namespace ANZ\Appointment\Service\OneC;

use ANZ\Appointment\Helper\Orm;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Result;
use Bitrix\Main\Error;
use Exception;
use ANZ\Appointment\Config\Constants;
use ANZ\Appointment\Tools\Utils;
use SoapVar;

Loc::loadMessages(__FILE__);

/**
 * Class Writer
 * @package ANZ\Appointment\Service\OneC
 */
class Writer extends BaseService
{
    /** make request to creating order
     * @param array $params
     * @return \Bitrix\Main\Result
     */
    public function addOrder(array $params): Result
    {
        try {
            if ($this->demoMode){
                sleep(3);
                return (new Result())->setData(['success' => true]);
            }

            $properties = $this->prepareCustomParams($params);

            $paramsToReserve = [
                'Specialization' => $params['specialty'] ?? "",
                'Date'           => $params['orderDate'],
                'TimeBegin'      => $params['timeBegin'],
                'EmployeeID'     => $params['refUid'],
                'Clinic'         => $params['clinicUid'],
            ];

            $xml_id = $this->getReserveUid($paramsToReserve);

            if (!strlen($xml_id) > 0){
                throw new Exception(Loc::getMessage("ANZ_APPOINTMENT_RESERVE_ERROR"));
            }

            $paramsToSend = [
                'EmployeeID'        => $params['refUid'],
                'PatientSurname'    => $params['surname'],
                'PatientName'       => $params['name'],
                'PatientFatherName' => $params['middleName'],
                'Date'              => $params['orderDate'],
                'TimeBegin'         => $params['timeBegin'],
                'Comment'           => $params['comment'] ?? '',
                'Phone'             => Utils::formatPhone($params['phone']),
                'Email'             => $params['email'] ?? '',
                'Address'           => $params['address'] ?? '',
                'Clinic'            => $params['clinicUid'],
                'GUID'              => $xml_id,
                'Params'            => $properties
            ];

            $res = $this->send(Constants::CREATE_ORDER_ACTION_1C, $paramsToSend);
            if ($res->isSuccess()){
                Orm::addRecord(array_merge($params, ['orderUid' => $xml_id]));
            }
            else
            {
                $this->deleteOrder($xml_id);
            }
            return $res;
        }
        catch (Exception $e){
            return (new Result())->addError(new Error($e->getMessage()));
        }
    }

    /**
     * @param array $params
     * @return string
     * @throws \Exception
     */
    public function getReserveUid(array $params): string
    {
        $res = $this->send(Constants::CREATE_RESERVE_ACTION_1C, $params);
        if ($res->isSuccess()){
            $data = $res->getData();
            return !empty($data['XML_ID']) ? $data['XML_ID'] : "";
        }
        else
        {
            throw new Exception(implode('; ', $res->getErrorMessages()));
        }
    }

    public function addWaitingList(array $params): Result
    {
        try {
            if ($this->demoMode){
                sleep(3);
                return (new Result())->setData(['success' => true]);
            }

            $paramsToSend = [
                'Specialization'    => $params['specialty'] ?? "",
                'PatientSurname'    => $params['surname'],
                'PatientName'       => $params['name'],
                'PatientFatherName' => $params['middleName'],
                'Date'              => $params['orderDate'],
                'TimeBegin'         => $params['timeBegin'],
                'Phone'             => Utils::formatPhone($params['phone']),
                'Email'             => $params['email'] ?? '',
                'Address'           => $params['address'] ?? '',
                'Clinic'            => $params['clinicUid'],
                'Comment'           => Loc::getMessage('ANZ_APPOINTMENT_WAITING_LIST_COMMENT', [
                    '#FULL_NAME#' => $params['name'] ." ". $params['middleName'] ." ". $params['surname'],
                    '#PHONE#'     => Utils::formatPhone($params['phone']),
                    '#DATE#'      => date("d.m.Y", strtotime($params['orderDate'])),
                    '#TIME#'      => date("H:i", strtotime($params['timeBegin'])),
                    '#COMMENT#'   => $params['comment'] ?? '',
                ]),
            ];

            return $this->send(Constants::CREATE_WAIT_LIST_ACTION_1C, $paramsToSend);
        }
        catch (Exception $e){
            return (new Result())->addError(new Error($e->getMessage()));
        }
    }

    /**
     * @param string $orderUid
     * @return \Bitrix\Main\Result
     * @throws \Exception
     */
    public function deleteOrder(string $orderUid): Result
    {
        if ($this->demoMode){
            throw new Exception('Can not use this request when DemoMode is ON');
        }
        return $this->send(Constants::DELETE_ORDER_ACTION_1C, ['GUID' => $orderUid]);
    }

    /**
     * @param array $params
     * @return array
     * @throws \Exception
     */
    private function prepareCustomParams(array $params): array
    {
        $properties = [];

        if (!empty($params['birthday']))
        {
            $properties[] = new SoapVar(
                '<ns2:Property name="Birthday"><ns2:Value>'.$params['birthday'].'</ns2:Value></ns2:Property>',
                XSD_ANYXML
            );
        }

        $duration = (int)$params["serviceDuration"] > 0
            ? Utils::calculateDurationFromSeconds((int)$params["serviceDuration"])
            : Utils::calculateDurationFromInterval($params['timeBegin'], $params['timeEnd']);
        $properties[] = new SoapVar(
            '<ns2:Property name="Duration"><ns2:Value>'.$duration.'</ns2:Value></ns2:Property>',
            XSD_ANYXML
        );

        if (!empty($params['serviceUid']))
        {
            $properties[] = new SoapVar(
                '<ns2:Property name="DurationType"><ns2:Value>ServiceDuration</ns2:Value></ns2:Property>',
                XSD_ANYXML
            );

            $properties[] = new SoapVar(
                '<ns2:Property name="Services"><ns2:Value>'.$params['serviceUid'].'</ns2:Value></ns2:Property>',
                XSD_ANYXML
            );
        }

        if (is_array($params['customParams']) && !empty($params['customParams']))
        {
            foreach ($params['customParams'] as $paramName => $paramValue) {
                $properties[] = new SoapVar(
                    '<ns2:Property name="'.$paramName.'"><ns2:Value>'.$paramValue.'</ns2:Value></ns2:Property>',
                    XSD_ANYXML
                );
            }
        }

        return $properties;
    }
}