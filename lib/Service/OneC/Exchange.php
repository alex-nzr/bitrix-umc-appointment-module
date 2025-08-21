<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.07.2022
 * ==================================================
*/
namespace ANZ\Appointment\Service\OneC;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Config\Constants;
use ANZ\Appointment\Internals\ServiceManager;
use ANZ\Appointment\Service\Cache\Manager as CacheManager;
use ANZ\Appointment\Service\Cache\Validator as CacheValidator;
use ANZ\Appointment\Service\Container;
use ANZ\Appointment\Tools\Utils;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Result;
use Exception;
use Throwable;


/**
 * @class Exchange
 * @package ANZ\Appointment\Service\OneC
 */
class Exchange extends Base
{
    protected array $demoData;
    protected string $cacheDir;
    protected string $clinicsFileName = 'clinics.json';
    protected string $employeesFileName = 'employees.json';
    protected string $nomenclatureFileName = 'nomenclature.json';
    protected string $scheduleFileName = 'schedule.json';

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        if ($this->demoMode)
        {
            $demoDataFile = Application::getDocumentRoot() . Configuration::getInstance()->getDemoDataFilePath();
            if (is_file($demoDataFile))
            {
                $this->demoData = json_decode(file_get_contents($demoDataFile), true);
            }
            else
            {
                $this->demoData = [];
                throw new Exception('Demo data file not found');
            }
        }

        $this->cacheDir = Application::getDocumentRoot() . Configuration::getInstance()->getCacheDir();
    }

    /**
     * @return \Bitrix\Main\Result
     */
    public function getClinicsList(): Result
    {
        if ($this->demoMode){
            $res = new Result();
            sleep(1);
            try {
                $res->setData($this->demoData['clinics']);
            }catch (Exception $e){
                $res->addError(new Error(Loc::getMessage("ANZ_APPOINTMENT_DEMO_MODE_ERROR") . $e->getMessage()));
            }
            return $res;
        }

        try
        {
            if (!CacheValidator::validate($this->cacheDir . $this->clinicsFileName))
            {
                $result = Utils::convertSdkResultToBitrixResult(Container::getInstance()->getSdkExchangeService()->getClinics());
                if ($result->isSuccess())
                {
                    CacheManager::getInstance()->saveJsonCache($this->cacheDir . $this->clinicsFileName, $result->getData());
                }
            }
            else
            {
                $result = (new Result)->setData(CacheManager::getInstance()->getJsonCacheData($this->cacheDir . $this->clinicsFileName));
            }
            return $result;
        }
        catch (Throwable $e)
        {
            return (new Result)->addError(new Error($e->getMessage()));
        }
    }

    /**
     * @return \Bitrix\Main\Result
     */
    public function getEmployeesList(): Result
    {
        if ($this->demoMode){
            $res = new Result();
            sleep(1);
            try {
                $res->setData($this->demoData['employees']);
            }catch (Exception $e){
                $res->addError(new Error(Loc::getMessage("ANZ_APPOINTMENT_DEMO_MODE_ERROR") . $e->getMessage()));
            }
            return $res;
        }

        try
        {
            $sdkResult = Container::getInstance()->getSdkExchangeService()->getEmployees();
            return Utils::convertSdkResultToBitrixResult($sdkResult);
        }
        catch (Throwable $e)
        {
            return (new Result)->addError(new Error($e->getMessage()));
        }
    }

    /**
     * @param string $clinicGuid
     * @return \Bitrix\Main\Result
     */
    public function getNomenclatureList(string $clinicGuid): Result
    {
        if ($this->demoMode){
            $res = new Result();
            sleep(1);
            try {
                $res->setData($this->demoData['services']);
            }catch (Exception $e){
                $res->addError(new Error(Loc::getMessage("ANZ_APPOINTMENT_DEMO_MODE_ERROR") . $e->getMessage()));
            }
            return $res;
        }

        try
        {
            $sdkResult = Container::getInstance()->getSdkExchangeService()->getNomenclature($clinicGuid);
            return Utils::convertSdkResultToBitrixResult($sdkResult);
        }
        catch (Throwable $e)
        {
            return (new Result)->addError(new Error($e->getMessage()));
        }
    }

    /**
     * @param array $params
     * @return \Bitrix\Main\Result
     */
    public function getSchedule(array $params = []): Result
    {
        if (!key_exists('clinicUid', $params))
        {
            $params['clinicUid'] = '';
        }

        if (!key_exists('employees', $params) || !is_array($params['employees']))
        {
            $params['employees'] = [];
        }

        if ($this->demoMode)
        {
            $res = new Result();
            sleep(1);
            try
            {
                $resultSchedule = [];
                if (!empty($params['clinicUid']) || !empty($params['employees']))
                {
                    if (key_exists($params['clinicUid'], $this->demoData['schedule']))
                    {
                        $clinicSchedule = $this->demoData['schedule'][$params['clinicUid']];
                        if (!is_array($clinicSchedule))
                        {
                            $clinicSchedule = [];
                        }

                        if (empty($params['employees']))
                        {
                            $resultSchedule[$params['clinicUid']] = $clinicSchedule;
                        }
                        else
                        {
                            foreach ($clinicSchedule as $specialtyKey => $specialtySchedule)
                            {
                                if(is_array($specialtySchedule))
                                {
                                    foreach ($params['employees'] as $employeeGuid)
                                    {
                                        if (key_exists($employeeGuid, $specialtySchedule))
                                        {
                                            if (!is_array($resultSchedule[$params['clinicUid']]))
                                            {
                                                $resultSchedule[$params['clinicUid']] = [];
                                            }

                                            if (!is_array($resultSchedule[$params['clinicUid']][$specialtyKey]))
                                            {
                                                $resultSchedule[$params['clinicUid']][$specialtyKey] = [];
                                            }

                                            $resultSchedule[$params['clinicUid']][$specialtyKey][$employeeGuid] = $specialtySchedule[$employeeGuid];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $res->setData($resultSchedule);
            }
            catch (Exception $e)
            {
                $res->addError(new Error(Loc::getMessage("ANZ_APPOINTMENT_DEMO_MODE_ERROR") . $e->getMessage()));
            }
            return $res;
        }

        try
        {
            $days = Configuration::getInstance()->getExchangeSchedulePeriod();

            if (!key_exists('clinicUid', $params))
            {
                $params['clinicUid'] = '';
            }

            if (!key_exists('employees', $params) || !is_array($params['employees']))
            {
                $params['employees'] = [];
            }

            $sdkResult = Container::getInstance()->getSdkExchangeService()->getSchedule(
                $days > 0 ? $days : 14, (string)$params['clinicUid'], $params['employees']
            );
            return Utils::convertSdkResultToBitrixResult($sdkResult);
        }
        catch (Throwable $e)
        {
            return (new Result)->addError(new Error($e->getMessage()));
        }
    }
}