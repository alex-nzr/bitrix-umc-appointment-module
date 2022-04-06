<?php
namespace FirstBit\Appointment\Services;

use Bitrix\Main\Localization\Loc;
use Exception;
use FirstBit\Appointment\Config\Constants;
use FirstBit\Appointment\Utils\Utils;
use SoapVar;

Loc::loadMessages(__FILE__);

class OneCWriter extends AbstractOneCService
{
    /** make request to creating order
     * @param array $params
     * @return array
     */
    public function addOrder(array $params): array
    {
        try {
            /*
         <ru:EmployeeID>ac30e139-3087-11dc-8594-005056c00008</ru:EmployeeID>
         <ru:PatientSurname>Иванов</ru:PatientSurname>
         <ru:PatientName>Иван</ru:PatientName>
         <ru:PatientFatherName>Иванович</ru:PatientFatherName>
         <ru:Date>2017-03-17T00:00:00</ru:Date>
         <ru:TimeBegin>0001-01-01T13:10:00</ru:TimeBegin>
         <ru:Comment>Комментарий</ru:Comment>
         <ru:Phone>89876543210</ru:Phone>
         <ru:Email>bit@1cbit.ru</ru:Email>
         <ru:Address>Ленина 1</ru:Address>
         <ru:Clinic>f679444a-22b7-11df-8618-002618dcef2c</ru:Clinic>
         <ru:GUID>9cc6b9fc-0b04-11e7-b13c-00e051000230</ru:GUID>
         <ru:Params>
             <core:Property name="Birthday">
                <core:Value>1980-09-14T00:00:00</core:Value>
             </core:Property>
             <core:Property name="Duration">
                <core:Value>0001-01-01T01:30:00</core:Value>
             </core:Property>
         </ru:Params>

         */
            /*
            {

                "serviceUid": "5210c9d4-65a2-11e9-936d-1856809fe650",
                "serviceDuration": "2700",

            }
            */

            if (Constants::DEMO_MODE === "Y"){
                sleep(3);
                return ['success' => true];
            }

            $properties = [];

            if (!empty($params['birthday']))
            {   //'1980-09-14T00:00:00' - format
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
                'GUID'              => "", //id of reserve if it available
                'Params'            => $properties
            ];

            return $this->send(Constants::CREATE_ORDER_ACTION_1C, $paramsToSend);
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
        return $this->send(Constants::DELETE_ORDER_ACTION_1C, [$orderUid]);
    }

    // TODO - make methods for adding WaitList and Reserve
}