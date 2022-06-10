<?php
namespace FirstBit\Appointment\Services\Operation;

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Bitrix\Main\Type\DateTime;
use Exception;
use FirstBit\Appointment\Services\Container;
use FirstBit\Appointment\Utils\Utils;

class OrmOperation{
    /**
     * @param array $params
     * @return \Bitrix\Main\Result
     */
    public static function addRecord(array $params): Result
    {
        $addResult = new Result();
        try {
            $recordDataClass = Container::getInstance()->getRecordDataClass();
            $newRecord = $recordDataClass::createObject();
            $newRecord->setXmlId($params['orderUid']);
            $newRecord->setClinicTitle($params['clinicName']);
            $newRecord->setSpecialty($params['specialty']);
            $newRecord->setDoctorName($params['doctorName']);
            $newRecord->setServiceTitle($params['serviceName']);
            $newRecord->setDatetimeVisit(DateTime::createFromTimestamp(strtotime($params['timeBegin'])));
            $newRecord->setPatientName($params['surname'] ." ". $params['name'] ." ". $params['middleName']);
            $newRecord->setPatientPhone(Utils::formatPhone($params['phone']));
            $newRecord->setPatientEmail($params['email']);
            $newRecord->setComment($params['comment']);
            $newRecord->setUserId(CurrentUser::get()->getId() ?? 0);

            $result = $newRecord->save();

            if ($result->isSuccess()){
                $addResult->setData(['ID' => $result->getId()]);
            }
            else{
                $addResult->addErrors($result->getErrorMessages());
            }
        }
        catch(Exception $e){
            $addResult->addError(new Error($e->getMessage()));
        }
        return $addResult;
    }

    /**
     * @param int $id
     * @param array $params
     * @return \Bitrix\Main\Result
     */
    public static function updateRecord(int $id, array $params): Result
    {
        $updateResult = new Result();
        try {
            $recordDataClass = Container::getInstance()->getRecordDataClass();
            $record = $recordDataClass::getByPrimary($id)->fetchObject();
            foreach ($params as $key => $value) {
                $record->set($key, $value);
            }
            $result = $record->save();

            if ($result->isSuccess()){
                $updateResult->setData(['ID' => $result->getData()]);
            }
            else{
                $updateResult->addErrors($result->getErrorMessages());
            }
        }
        catch(Exception $e){
            $updateResult->addError(new Error($e->getMessage()));
        }
        return $updateResult;
    }

    /**
     * @param int $id
     * @return \Bitrix\Main\Result
     */
    public static function deleteRecord(int $id): Result
    {
        $deleteResult = new Result();
        try {
            $recordDataClass = Container::getInstance()->getRecordDataClass();
            $record = $recordDataClass::getByPrimary($id)->fetchObject();
            $result = $record->delete();
            if ($result->isSuccess()){
                $deleteResult->setData(['message' => 'record with id = "'.$id.'" was deleted']);
            }
            else{
                $deleteResult->addErrors($result->getErrorMessages());
            }
        }
        catch(Exception $e){
            $deleteResult->addError(new Error($e->getMessage()));
        }
        return $deleteResult;
    }
}