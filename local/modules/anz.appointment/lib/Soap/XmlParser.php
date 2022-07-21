<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - XmlParser.php
 * 10.07.2022 22:37
 * ==================================================
 */
namespace ANZ\Appointment\Soap;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Result;
use Exception;
use ANZ\Appointment\Config\Constants;
use ANZ\Appointment\Event\Event;
use ANZ\Appointment\Event\EventType;
use ANZ\Appointment\Tools\Utils;
use SimpleXMLElement;

Loc::loadMessages(__FILE__);

/**
 * Class XmlParser
 * @package ANZ\Appointment\Soap
 */
class XmlParser{

    protected array $fieldMap;

    public function __construct()
    {
        $this->fieldMap = [
            'CLINIC' => [
                'CLINIC_KEY'    => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_CLINIC_KEY'),
                'CLINIC_TITLE'  => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_CLINIC_TITLE'),
                'CLINIC_UID'    => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_CLINIC_UID'),
            ],
            'EMPLOYEE' => [
                'EMPLOYEE_KEY' => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_EMPLOYEE_KEY'),
                'ORGANIZATION' => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_ORGANIZATION'),
                'NAME'         => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_NAME'),
                'LAST_NAME'    => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_LAST_NAME'),
                'MIDDLE_NAME'  => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_MIDDLE_NAME'),
                'PHOTO'        => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_PHOTO'),
                'DESCRIPTION'  => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_DESCRIPTION'),
                'SPECIALTY'    => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_SPECIALTY'),
                'SERVICES'     => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_SERVICES'),
                'SERVICE'      => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_SERVICE'),
                'DURATION'     => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_EMPLOYEE_DURATION'),
            ],
            'NOMENCLATURE' => [
                'CATALOG'    => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_CATALOG'),
                'IS_FOLDER'  => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_IS_FOLDER'),
                'TITLE'      => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_TITLE'),
                'TYPE'       => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_TYPE'),
                'ART_NUMBER' => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_ART_NUMBER'),
                'PRICE'      => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_PRICE'),
                'DURATION'   => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_DURATION'),
            ],
            'SCHEDULE' => [
                'SCHEDULE_FOR_SITE'           => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_SCHEDULE_FOR_SITE'),
                'SCHEDULE_EMPLOYEE_UID'       => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_SCHEDULE_EMPLOYEE_UID'),
                'SCHEDULE_EMPLOYEE_FULL_NAME' => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_SCHEDULE_EMPLOYEE_FULL_NAME'),
                'SCHEDULE_DURATION'           => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_SCHEDULE_DURATION'),
                'SCHEDULE_PERIODS'            => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_SCHEDULE_PERIODS'),
                'SCHEDULE_PERIOD'             => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_SCHEDULE_PERIOD'),
                'SCHEDULE_FREE'               => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_SCHEDULE_FREE'),
                'SCHEDULE_BUSY'               => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_SCHEDULE_BUSY'),
                'SCHEDULE_START'              => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_SCHEDULE_START'),
                'SCHEDULE_END'                => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_SCHEDULE_END'),
                'SCHEDULE_DATE'               => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_SCHEDULE_DATE'),
                'SCHEDULE_TIME_TYPE'          => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_SCHEDULE_TIME_TYPE'),
            ],
            'COMMON' => [
                'COMMON_RES_FLAG'    => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_COMMON_RES_FLAG'),
                'COMMON_RES_DESC'    => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_COMMON_RES_DESC'),
                'COMMON_ERROR_DESC'  => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_COMMON_ERROR_DESC'),
                'COMMON_BOOKING_UID' => Loc::getMessage('ANZ_APPOINTMENT_XML_PARSER_COMMON_BOOKING_UID'),
            ],
        ];
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return \Bitrix\Main\Result
     */
    public function prepareClinicData(SimpleXMLElement $xml): Result
    {
        $result = new Result();
        try
        {
            $xmlArr = $this->xmlToArray($xml);
            $xmlArr = Event::getEventHandlersResult(EventType::ON_BEFORE_CLINICS_PARSED, $xmlArr);

            $clinicKey      = $this->fieldMap['CLINIC']['CLINIC_KEY'];
            $clinicTitleKey = $this->fieldMap['CLINIC']['CLINIC_TITLE'];
            $clinicUidKey   = $this->fieldMap['CLINIC']['CLINIC_UID'];

            $clinics = [];
            if (is_array($xmlArr[$clinicKey]))
            {
                if (Utils::is_assoc($xmlArr[$clinicKey]))
                {
                    $clinics[$xmlArr[$clinicKey][$clinicUidKey]] = [
                        'uid' => $xmlArr[$clinicKey][$clinicUidKey],
                        'name' => $xmlArr[$clinicKey][$clinicTitleKey]
                    ];
                }
                else
                {
                    foreach ($xmlArr[$clinicKey] as $item) {
                        $clinic = [];
                        $clinic['uid'] = $item[$clinicUidKey];
                        $clinic['name'] = $item[$clinicTitleKey];
                        $clinics[$item[$clinicUidKey]] = $clinic;
                    }
                }
            }
            $result->setData(
                (array)Event::getEventHandlersResult(EventType::ON_AFTER_CLINICS_PARSED, $clinics)
            );
        }
        catch (Exception $e)
        {
            $result->addError(new Error($e->getMessage()));
        }
        return $result;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return \Bitrix\Main\Result
     */
    public function prepareEmployeesData(SimpleXMLElement $xml): Result
    {
        $result = new Result();
        try
        {
            $xmlArr = $this->xmlToArray($xml);
            $xmlArr = Event::getEventHandlersResult(EventType::ON_BEFORE_EMPLOYEES_PARSED, $xmlArr);

            $employeeKey     = $this->fieldMap['EMPLOYEE']['EMPLOYEE_KEY'];
            $organizationKey = $this->fieldMap['EMPLOYEE']['ORGANIZATION'];
            $nameKey         = $this->fieldMap['EMPLOYEE']['NAME'];
            $lastNameKey     = $this->fieldMap['EMPLOYEE']['LAST_NAME'];
            $middleNameKey   = $this->fieldMap['EMPLOYEE']['MIDDLE_NAME'];
            $photoKey        = $this->fieldMap['EMPLOYEE']['PHOTO'];
            $descriptionKey  = $this->fieldMap['EMPLOYEE']['DESCRIPTION'];
            $specialtyKey    = $this->fieldMap['EMPLOYEE']['SPECIALTY'];
            $servicesKey     = $this->fieldMap['EMPLOYEE']['SERVICES'];
            $oneServiceKey   = $this->fieldMap['EMPLOYEE']['SERVICE'];
            $durationKey     = $this->fieldMap['EMPLOYEE']['DURATION'];

            $employees = [];
            if (is_array($xmlArr[$employeeKey]))
            {
                foreach ($xmlArr[$employeeKey] as $item)
                {
                    $employee = [];
                    $clinicUid = ($item[$organizationKey] == "00000000-0000-0000-0000-000000000000") ? "" : $item[$organizationKey];
                    $uid = is_array($item['UID']) ? current($item['UID']) : $item['UID'];

                    $employee['uid']          = $uid;
                    $employee['name']         = $item[$nameKey];
                    $employee['surname']      = $item[$lastNameKey];
                    $employee['middleName']   = $item[$middleNameKey];
                    $employee['fullName']     = $item[$lastNameKey] ." ". $item[$nameKey] ." ". $item[$middleNameKey];
                    $employee['clinicUid']    = $clinicUid;
                    $employee['photo']        = $item[$photoKey];
                    $employee['description']  = $item[$descriptionKey];
                    $employee['specialty']    = $item[$specialtyKey];
                    $employee['specialtyUid'] = base64_encode($item[$specialtyKey]);
                    $employee['services']     = [];

                    if (is_array($item[$servicesKey][$oneServiceKey]))
                    {
                        foreach ($item[$servicesKey][$oneServiceKey] as $service)
                        {
                            if (!empty($service['UID']))
                            {
                                $employee['services'][$service['UID']] = [
                                    'title'            => 'this field not exists in typical integration',
                                    'personalDuration' => strtotime($service[$durationKey])-strtotime('0001-01-01T00:00:00')
                                ];
                            }
                        }
                    }

                    $employees[$uid] = $employee;
                }
            }
            $result->setData(
                (array)Event::getEventHandlersResult(EventType::ON_AFTER_EMPLOYEES_PARSED, $employees)
            );
        }
        catch (Exception $e)
        {
            $result->addError(new Error($e->getMessage()));
        }
        return $result;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return \Bitrix\Main\Result
     */
    public function prepareNomenclatureData(SimpleXMLElement $xml): Result
    {
        $result = new Result();
        try
        {
            $xmlArr = $this->xmlToArray($xml);
            $xmlArr = Event::getEventHandlersResult(EventType::ON_BEFORE_NOMENCLATURE_PARSED, $xmlArr);

            $catalogKey     = $this->fieldMap['NOMENCLATURE']['CATALOG'];
            $isFolderKey    = $this->fieldMap['NOMENCLATURE']['IS_FOLDER'];
            $titleKey       = $this->fieldMap['NOMENCLATURE']['TITLE'];
            $typeKey        = $this->fieldMap['NOMENCLATURE']['TYPE'];
            $artNumberKey   = $this->fieldMap['NOMENCLATURE']['ART_NUMBER'];
            $priceKey       = $this->fieldMap['NOMENCLATURE']['PRICE'];
            $durationKey    = $this->fieldMap['NOMENCLATURE']['DURATION'];

            $nomenclature = [];
            if (is_array($xmlArr[$catalogKey]))
            {
                foreach ($xmlArr[$catalogKey] as $item)
                {
                    if ($item[$isFolderKey] === true){
                        continue;
                    }
                    $uid = is_array($item['UID']) ? current($item['UID']) : $item['UID'];

                    $product = [];
                    $product['uid']         = $uid;
                    $product['name']        = $item[$titleKey];
                    $product['typeOfItem']  = $item[$typeKey];
                    $product['artNumber']   = $item[$artNumberKey];
                    $product['price']       = str_replace("[^0-9]", '', $item[$priceKey]);
                    $product['duration']    = Utils::formatDurationToSeconds($item[$durationKey]);
                    $nomenclature[$uid]     = $product;
                }
            }
            $result->setData(
                (array)Event::getEventHandlersResult(EventType::ON_AFTER_NOMENCLATURE_PARSED, $nomenclature)
            );
        }
        catch (Exception $e)
        {
            $result->addError(new Error($e->getMessage()));
        }
        return $result;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return \Bitrix\Main\Result
     */
    public function prepareScheduleData(SimpleXMLElement $xml): Result
    {
        $result = new Result();
        try
        {
            $xmlArr = $this->xmlToArray($xml);
            $xmlArr = Event::getEventHandlersResult(EventType::ON_BEFORE_SCHEDULE_PARSED, $xmlArr);

            $scheduleKey = $this->fieldMap['SCHEDULE']['SCHEDULE_FOR_SITE'];

            $schedule = [];
            if (is_array($xmlArr[$scheduleKey])){
                $schedule = $this->processScheduleData($xmlArr[$scheduleKey]);
            }
            $result->setData(
                (array)Event::getEventHandlersResult(EventType::ON_AFTER_SCHEDULE_PARSED, $schedule)
            );
        }
        catch (Exception $e)
        {
            $result->addError(new Error($e->getMessage()));
        }
        return $result;
    }

    /**
     * prepare schedule data for frontend
     * @param array $schedule
     * @return array
     */
    public function processScheduleData(array $schedule): array
    {
        if (Utils::is_assoc($schedule))
        {
            $schedule = [$schedule];
        }

        $employeeUidKey       = $this->fieldMap['SCHEDULE']['SCHEDULE_EMPLOYEE_UID'];
        $employeeFullNameKey  = $this->fieldMap['SCHEDULE']['SCHEDULE_EMPLOYEE_FULL_NAME'];
        $scheduleDurationKey  = $this->fieldMap['SCHEDULE']['SCHEDULE_DURATION'];
        $schedulePeriodsKey   = $this->fieldMap['SCHEDULE']['SCHEDULE_PERIODS'];
        $scheduleOnePeriodKey = $this->fieldMap['SCHEDULE']['SCHEDULE_PERIOD'];
        $scheduleFreeTimeKey  = $this->fieldMap['SCHEDULE']['SCHEDULE_FREE'];
        $scheduleBusyTimeKey  = $this->fieldMap['SCHEDULE']['SCHEDULE_BUSY'];
        $specialtyKey         = $this->fieldMap['EMPLOYEE']['SPECIALTY'];
        $clinicKey            = $this->fieldMap['CLINIC']['CLINIC_KEY'];

        $formattedSchedule = [];
        foreach ($schedule as $key => $item)
        {
            if (isset($item[$employeeUidKey])){
                $formattedSchedule[$key]["refUid"] = $item[$employeeUidKey];
            }
            if (isset($item[$specialtyKey])){
                $formattedSchedule[$key]["specialty"] = $item[$specialtyKey];
            }
            if (isset($item[$employeeFullNameKey])){
                $formattedSchedule[$key]["name"] = $item[$employeeFullNameKey];
            }
            if (isset($item[$clinicKey])){
                $formattedSchedule[$key]["clinicUid"] = $item[$clinicKey];
            }

            $duration = 0;
            if (isset($item[$scheduleDurationKey])){
                $formattedSchedule[$key]["duration"] = $item[$scheduleDurationKey];
                $duration = intval(date("H", strtotime($item[$scheduleDurationKey]))) * 3600
                    + intval(date("i", strtotime($item[$scheduleDurationKey]))) * 60;
                $formattedSchedule[$key]["durationInSeconds"] = $duration;
            }

            $freeTime = (is_array($item[$schedulePeriodsKey][$scheduleFreeTimeKey]) && count($item[$schedulePeriodsKey][$scheduleFreeTimeKey]) > 0)
                ? $item[$schedulePeriodsKey][$scheduleFreeTimeKey][$scheduleOnePeriodKey] : [];
            $busyTime = (is_array($item[$schedulePeriodsKey][$scheduleBusyTimeKey]) && count($item[$schedulePeriodsKey][$scheduleBusyTimeKey]) > 0)
                ? $item[$schedulePeriodsKey][$scheduleBusyTimeKey][$scheduleOnePeriodKey] : [];

            if (Utils::is_assoc($freeTime)) {
                $freeTime = array($freeTime);
            }
            if (Utils::is_assoc($busyTime)) {
                $busyTime = array($busyTime);
            }

            $formattedSchedule[$key]["timetable"]["free"] = $this->formatTimetable($freeTime, $duration);
            $formattedSchedule[$key]["timetable"]["busy"] = $this->formatTimetable($busyTime, 0, true);
            $formattedSchedule[$key]["timetable"]["freeNotFormatted"] = $this->formatTimetable($freeTime, 0, true);
        }
        return [
            "schedule" => $formattedSchedule,
        ];
    }

    /** Beautify array of timelines
     * @param $array
     * @param int $duration
     * @param bool $useDefaultInterval
     * @return array
     */
    public function formatTimetable($array, int $duration, $useDefaultInterval = false): array
    {
        if (!is_array($array) || empty($array)){
            return [];
        }

        if (!$duration > 0){
            $duration = Option::get(
                Constants::APPOINTMENT_MODULE_ID,
                'appointment_settings_default_duration',
                Constants::DEFAULT_APPOINTMENT_DURATION_SEC
            );
        }

        if (!empty($array))
        {
            if (Utils::is_assoc($array)) {
                $array = [$array];
            }

            $scheduleStartKey = $this->fieldMap['SCHEDULE']['SCHEDULE_START'];
            $scheduleEndKey   = $this->fieldMap['SCHEDULE']['SCHEDULE_END'];

            $formattedArray = [];
            foreach ($array as $item)
            {
                $timestampTimeBegin = strtotime($item[$scheduleStartKey]);
                $timestampTimeEnd = strtotime($item[$scheduleEndKey]);

                if ($useDefaultInterval)
                {
                    $formattedArray[] = $this->formatTimeTableItem($item, (int)$timestampTimeBegin, (int)$timestampTimeEnd);
                }
                else
                {
                    $timeDifference = $timestampTimeEnd - $timestampTimeBegin;
                    $appointmentsCount = round($timeDifference / $duration);

                    for ($i = 0; $i < $appointmentsCount; $i++)
                    {
                        $start = $timestampTimeBegin + ($duration * $i);
                        $end = $timestampTimeBegin + ($duration * ($i+1));

                        $formattedArray[] = $this->formatTimeTableItem($item, (int)$start, (int)$end);
                    }
                }
            }
            return $formattedArray;
        }
        else
        {
            return [];
        }
    }

    /**
     * @param array $item
     * @param int $start
     * @param int $end
     * @return array
     */
    public function formatTimeTableItem(array $item, int $start, int $end): array
    {
        $scheduleDateKey     = $this->fieldMap['SCHEDULE']['SCHEDULE_DATE'];
        $scheduleTimeTypeKey = $this->fieldMap['SCHEDULE']['SCHEDULE_TIME_TYPE'];

        return [
            "typeOfTimeUid" => $item[$scheduleTimeTypeKey],
            "date" => $item[$scheduleDateKey],
            "timeBegin" => date("Y-m-d", $start) ."T". date("H:i:s", $start),
            "timeEnd" => date("Y-m-d", $end) ."T". date("H:i:s", $end),
            "formattedDate" => date("d-m-Y", strtotime($item[$scheduleDateKey])),
            "formattedTimeBegin" => date("H:i", $start),
            "formattedTimeEnd" => date("H:i", $end),
        ];
    }

    /**
     * Parse result for add order, delete order and add wait list requests
     * @param \SimpleXMLElement $xml
     * @return \Bitrix\Main\Result
     */
    public function prepareCommonResultData(SimpleXMLElement $xml): Result
    {
        $result = new Result();
        $xmlArr = $this->xmlToArray($xml);

        $commonResKey        = $this->fieldMap['COMMON']['COMMON_RES_FLAG'];
        $commonErrDescKey    = $this->fieldMap['COMMON']['COMMON_ERROR_DESC'];

        if ($xmlArr[$commonResKey] === "true"){
            $result->setData(['success' => true]);
        }
        else {
            $result->addError(new Error((string)$xmlArr[$commonErrDescKey]));
        }
        return $result;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return \Bitrix\Main\Result
     */
    public function prepareReserveResultData(SimpleXMLElement $xml): Result
    {
        $result = new Result();
        $xmlArr = $this->xmlToArray($xml);

        $commonResKey        = $this->fieldMap['COMMON']['COMMON_RES_FLAG'];
        $commonErrDescKey    = $this->fieldMap['COMMON']['COMMON_ERROR_DESC'];
        $commonBookingUidKey = $this->fieldMap['COMMON']['COMMON_BOOKING_UID'];

        if ($xmlArr[$commonResKey] === "true" && !empty($xmlArr[$commonBookingUidKey])){
            $result->setData([
                'success' => true,
                'XML_ID'  => $xmlArr[$commonBookingUidKey]
            ]);
        }
        else {
            $result->addError(new Error((string)$xmlArr[$commonErrDescKey]));
        }
        return $result;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return \Bitrix\Main\Result
     */
    public function prepareStatusResultData(SimpleXMLElement $xml): Result
    {
        $result = new Result();
        $xmlArr = $this->xmlToArray($xml);

        $commonResKey        = $this->fieldMap['COMMON']['COMMON_RES_FLAG'];
        $commonResDescKey    = $this->fieldMap['COMMON']['COMMON_RES_DESC'];
        $commonErrDescKey    = $this->fieldMap['COMMON']['COMMON_ERROR_DESC'];

        if ((int)$xmlArr[$commonResKey] > 0)
        {
            $result->setData([
                'success'   => true,
                'statusId'  => $xmlArr[$commonResKey],
                'status'    => $xmlArr[$commonResDescKey]
            ]);
        }
        else {
            $result->addError(new Error($xmlArr[$commonResKey] ." - ". $xmlArr[$commonErrDescKey]));
        }
        return $result;
    }

    /**
     * @param SimpleXMLElement $xml
     * @return array
     */
    public function xmlToArray(SimpleXMLElement $xml): array
    {
        return json_decode(json_encode($xml), true);
    }
}