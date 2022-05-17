<?php
namespace FirstBit\Appointment\Services\Operation;

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Type\DateTime;
use Exception;
use FirstBit\Appointment\Model\RecordTable;
use FirstBit\Appointment\Utils\Utils;

class OrmOperation{
    /**
     * @param array $params
     * @return array
     */
    public static function addRecord(array $params): array
    {
        try {
            $paramsToAdd = [
                'XML_ID'         => $params['orderUid'],
                'CLINIC_TITLE'   => $params['clinicName'],
                'SPECIALTY'      => $params['specialty'],
                'DOCTOR_NAME'    => $params['doctorName'],
                'SERVICE_TITLE'  => $params['serviceName'],
                'DATETIME_VISIT' => DateTime::createFromTimestamp(strtotime($params['timeBegin'])),
                'PATIENT_NAME'   => $params['surname'] ." ". $params['name'] ." ". $params['middleName'],
                'PATIENT_PHONE'  => Utils::formatPhone($params['phone']),
                'PATIENT_EMAIL'  => $params['email'],
                'COMMENT'        => $params['comment'],
                'STATUS_1C'      => '',
                'USER_ID'        => CurrentUser::get()->getId() ?? 0,
            ];

            $result = RecordTable::add($paramsToAdd);

            if ($result->isSuccess()){
                return array_merge($result->getData(), ['ID' => $result->getId()]);
            }
            else{
                throw new Exception(implode("; ", $result->getErrorMessages()));
            }
        }
        catch(Exception $e){
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * @param int $id
     * @param array $params
     * @return array
     */
    public static function updateRecord(int $id, array $params): array
    {
        try {
            $result = RecordTable::update($id, $params);

            if ($result->isSuccess()){
                return array_merge($result->getData(), ['ID' => $result->getId()]);
            }
            else{
                throw new Exception(implode("; ", $result->getErrorMessages()));
            }
        }
        catch(Exception $e){
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * @param int $id
     * @return array
     */
    public static function deleteRecord(int $id): array
    {
        try {
            $result = RecordTable::delete($id);

            if ($result->isSuccess()){
                return array_merge($result->getData(), ['message' => 'record with id = "'.$id.'" was deleted']);
            }
            else{
                throw new Exception(implode("; ", $result->getErrorMessages()));
            }
        }
        catch(Exception $e){
            return [
                'error' => $e->getMessage()
            ];
        }
    }
}