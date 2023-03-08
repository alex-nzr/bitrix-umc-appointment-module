<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * Bit.Umc - Bitrix integration - FtpDataReader.php
 * 05.03.2023 21:05
 * ==================================================
 */
namespace ANZ\Appointment\Service\Xml;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Internals\Contract\IReaderService;
use ANZ\Appointment\Service\Container;
use Bitrix\Main\Application;
use Bitrix\Main\Result;
use SimpleXMLElement;

/**
 * @class FtpDataReader
 * @package ANZ\Appointment\Service\Xml
 */
class FtpDataReader implements IReaderService
{
    const EMPLOYEES_FILE_NAME    = 'Employees.xml';
    const NOMENCLATURE_FILE_NAME = 'Price.xml';
    const SCHEDULE_FILE_NAME     = 'Schedule.xml';

    private array $ftpDirectoriesMap;
    private ?string $docRoot;

    public function __construct()
    {
        $this->docRoot           = Application::getDocumentRoot();
        $this->ftpDirectoriesMap = Configuration::getInstance()->getFtpDirectoriesMap();
    }

    /**
     * @return \Bitrix\Main\Result
     */
    public function getClinicsList(): Result
    {
        $res = new Result();
        if (!empty($this->ftpDirectoriesMap))
        {
            $data = [];
            $root = Application::getDocumentRoot();
            foreach ($this->ftpDirectoriesMap as $uid => $path)
            {
                if (is_dir($root.$path))
                {
                    $arr  = explode('/', $path);
                    $name = end($arr);
                    $data[$uid] = [
                        'uid'  => $uid,
                        'name' => $name
                    ];
                }
            }
            $res->setData($data);
        }
        return $res;
    }

    /**
     * @param string|null $clinicGuid
     * @return \Bitrix\Main\Result
     * @throws \Exception
     */
    public function getEmployeesList(?string $clinicGuid = null): Result
    {
        $res = new Result();
        $data = [];

        if (!empty($clinicGuid) && array_key_exists($clinicGuid, $this->ftpDirectoriesMap))
        {
            $fileMap = [
                $clinicGuid => $this->ftpDirectoriesMap[$clinicGuid]
            ];
        }
        else
        {
            $fileMap = $this->ftpDirectoriesMap;
        }

        foreach ($fileMap as $uid => $path)
        {
            $pathToFile = $this->docRoot . $this->ftpDirectoriesMap[$uid] . '/' . static::EMPLOYEES_FILE_NAME;
            if (is_file($pathToFile))
            {
                $strXml = file_get_contents($pathToFile);
                $xmlObj = new SimpleXMLElement($strXml);
                $xmlRes = Container::getInstance()->getXmlParser()->prepareEmployeesData($xmlObj);
                if ($xmlRes->isSuccess())
                {
                    $data = array_merge($data, $xmlRes->getData());
                }
                else
                {
                    $res->addErrors($xmlRes->getErrors());
                }
            }
        }

        $res->setData($data);
        return $res;
    }

    /**
     * @param string $clinicGuid
     * @return \Bitrix\Main\Result
     * @throws \Exception
     */
    public function getNomenclatureList(string $clinicGuid): Result
    {
        $res = new Result();

        if (array_key_exists($clinicGuid, $this->ftpDirectoriesMap))
        {
            $pathToFile = $this->docRoot . $this->ftpDirectoriesMap[$clinicGuid] . '/' . static::NOMENCLATURE_FILE_NAME;
            if (is_file($pathToFile))
            {
                $strXml = file_get_contents($pathToFile);
                $xmlObj = new SimpleXMLElement($strXml);
                $xmlRes = Container::getInstance()->getXmlParser()->prepareNomenclatureData($xmlObj);
                if ($xmlRes->isSuccess())
                {
                    $res->setData($xmlRes->getData());
                }
                else
                {
                    $res->addErrors($xmlRes->getErrors());
                }
            }
        }

        return $res;
    }

    /**
     * @param array $params
     * @return \Bitrix\Main\Result
     * @throws \Exception
     */
    public function getSchedule(array $params = []): Result
    {
        $res  = new Result();

        if (array_key_exists('clinicUid', $params))
        {
            $fileMap = [
                $params['clinicUid'] => $this->ftpDirectoriesMap[$params['clinicUid']]
            ];
        }
        else
        {
            $fileMap = $this->ftpDirectoriesMap;
        }

        $data = [];
        foreach ($fileMap as $uid => $path)
        {
            $pathToFile = $this->docRoot . $this->ftpDirectoriesMap[$uid] . '/' . static::SCHEDULE_FILE_NAME;
            if (is_file($pathToFile))
            {
                $strXml = file_get_contents($pathToFile);
                $xmlObj = new SimpleXMLElement($strXml);
                $xmlRes = Container::getInstance()->getXmlParser()->prepareScheduleData($xmlObj);
                if ($xmlRes->isSuccess())
                {
                    $xmlData = $xmlRes->getData();
                    if (array_key_exists('schedule', $xmlData) && is_array($xmlData['schedule']))
                    {
                        $data = array_merge($data, $xmlData['schedule']);
                    }
                }
                else
                {
                    $res->addErrors($xmlRes->getErrors());
                }
            }
        }

        $res->setData(['schedule' => $data]);
        return $res;
    }
}