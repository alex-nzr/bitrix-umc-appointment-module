<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.07.2022
 * ==================================================
*/
namespace ANZ\Appointment\Model\EntityObject;

use ANZ\Appointment\Model\RecordTable;
use ANZ\Appointment\Service\Container;
use Bitrix\Main\ORM\Objectify\EntityObject;
use Bitrix\Main\Type\DateTime;

/**
 * @method Record setXmlId(string $value)
 * @method Record setClinicTitle(string $value)
 * @method Record setSpecialty(string $value)
 * @method Record setDoctorName(string $value)
 * @method Record setServiceTitle(string $value)
 * @method Record setDatetimeVisit(DateTime $value)
 * @method Record setPatientName(string $value)
 * @method Record setPatientPhone(string $value)
 * @method Record setPatientEmail(string $value)
 * @method Record setComment(string $value)
 * @method Record setUserId(int $value)
 */
class Record extends EntityObject
{
    /**
     * @var RecordTable|string
     */
    public static $dataClass = RecordTable::class;

    /**
     * @throws \Exception
     */
    public static function fromArray(array $data): static
    {
        return static::$dataClass::createObject()
                        ->setXmlId($data['bookingUid'])
                        ->setClinicTitle($data['clinicName'])
                        ->setSpecialty($data['specialty'])
                        ->setDoctorName($data['doctorName'])
                        ->setServiceTitle($data['serviceName'] ?? '')
                        ->setDatetimeVisit(DateTime::createFromTimestamp(strtotime($data['timeBegin'])))
                        ->setPatientName($data['surname'] ." ". $data['name'] ." ". $data['middleName'])
                        ->setPatientPhone($data['phone'])
                        ->setPatientEmail($data['email'] ?? '')
                        ->setComment($data['comment'] ?? '')
                        ->setUserId(Container::getInstance()->getUserPermissions()->getUserId());
    }

    /**
     * @throws \Exception
     */
    public function setStatus1c(string $status1c): Record
    {
        $this->set(static::$dataClass::FIELD_NAME_STATUS_1C, $status1c);
        return $this;
    }
}