<?php
namespace FirstBit\Appointment\Services\Operation;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Exception;
use FirstBit\Appointment\Config\Constants;
use FirstBit\Appointment\Services\OneCWriter;

class AppointmentOperation
{

    public static function addOrder(OneCWriter $writer, array $arParams): Result
    {
        $useWaitingList = Option::get(
            Constants::APPOINTMENT_MODULE_ID,
            'appointment_settings_use_waiting_list', "N"
        );

        if ($useWaitingList === "Y"){
            $response = $writer->addWaitingList($arParams);
        }
        else{
            $response = $writer->addOrder($arParams);
        }

        return $response;
    }

    public static function deleteOrder(OneCWriter $writer, int $id, string $orderUid): Result
    {
        try
        {
            $response = $writer->deleteOrder($orderUid);
            if ($response->isSuccess())
            {
                $ormRes = OrmOperation::deleteRecord($id);
                $data = $response->getData();
                $response->setData(array_merge($data, $ormRes));
                return $response;
            }
            else
            {
                throw new Exception(implode(", ", $response->getErrorMessages()));
            }
        }
        catch (Exception $e)
        {
            return (new Result)->addError(new Error($e->getMessage()));
        }
    }

    public static function getOrderStatus($reader, int $id, string $orderUid)
    {
        try
        {
            $response = $reader->getOrderStatus($orderUid);
            if ($response->isSuccess())
            {
                $data = $response->getData();
                $status = $data['status'] ?? "-";
                $ormRes = OrmOperation::updateRecord($id, ['STATUS_1C' => $status]);
                $response->setData(array_merge($data, $ormRes));
                return $response;
            }
            else
            {
                throw new Exception(implode(", ", $response->getErrorMessages()));
            }
        }
        catch (Exception $e)
        {
            return (new Result)->addError(new Error($e->getMessage()));
        }
    }
}