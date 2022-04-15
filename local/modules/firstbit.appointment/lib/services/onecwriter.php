<?php
namespace FirstBit\Appointment\Services;

use Bitrix\Main\Localization\Loc;
use Exception;
use FirstBit\Appointment\Config\Constants;
use FirstBit\Appointment\Utils\Utils;
use SoapVar;

Loc::loadMessages(__FILE__);

class OneCWriter extends BaseOneCService
{
    /** make request to creating order
     * @param array $params
     * @return array
     */
    public function addOrder(array $params): array
    {
        try {
            if (Constants::DEMO_MODE === "Y"){
                sleep(3);
                return ['success' => true];
            }

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

            if (!empty($params['serviceUid'])){
                $properties[] = new SoapVar(
                    '<ns2:Property name="Service"><ns2:Value>'.$params['serviceUid'].'</ns2:Value></ns2:Property>',
                    XSD_ANYXML
                );
            }

            $paramsToReserve = [
                'Specialization' => $params['specialty'] ?? "",
                'Date'           => $params['orderDate'],
                'TimeBegin'      => $params['timeBegin'],
                'EmployeeID'     => $params['refUid'],
                'Clinic'         => $params['clinicUid'],
            ];

            $xml_id = $this->getReserveUid($paramsToReserve);

            if (!strlen($xml_id) > 0){
                throw new Exception("FIRSTBIT_APPOINTMENT_RESERVE_ERROR");
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
            if (empty($res['error'])){
                RecordTableHelper::addRecord(array_merge($params, ['orderUid' => $xml_id]));
            }
            return $res;
        }
        catch (Exception $e){
            return Utils::createErrorArray($e->getMessage());
        }
    }

    /**
     * @param array $params
     * @return string
     */
    public function getReserveUid(array $params): string
    {
        $res = $this->send(Constants::CREATE_RESERVE_ACTION_1C, $params);
        if ($res['success'] && !empty($res['XML_ID'])){
            return $res['XML_ID'];
        }
        else
        {
            return "";
        }
    }

    public function addWaitingList(array $params): array
    {
        try {
            if (Constants::DEMO_MODE === "Y"){
                sleep(3);
                return ['success' => true];
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
                'Comment'           => Loc::getMessage('FIRSTBIT_APPOINTMENT_WAITING_LIST_COMMENT', [
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
            return Utils::createErrorArray($e->getMessage());
        }
    }

    /** cancelling order in 1C
     * @param string $orderUid
     * @return array
     */
    public function deleteOrder(string $orderUid): array
    {
        return $this->send(Constants::DELETE_ORDER_ACTION_1C, ['GUID' => $orderUid]);
    }
}