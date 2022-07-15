<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - Container.php
 * 10.07.2022 22:37
 * ==================================================
 */
namespace ANZ\Appointment\Services;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Localization\Loc;
use ANZ\Appointment\Model\RecordTable;
use ANZ\Appointment\Services\Message\MailerService;
use ANZ\Appointment\Services\Message\SmsService;
use ANZ\Appointment\Services\OneC\Reader;
use ANZ\Appointment\Services\OneC\Writer;

/**
 * Class Container
 * @package ANZ\Appointment\Services
 */
class Container
{
    protected ServiceLocator $serviceLocator;

    /**
     * Container constructor.
     */
    public function __construct(){
        $this->serviceLocator = ServiceLocator::getInstance();
    }

    /**
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function getInstance(): Container
    {
        $instanceId = static::getServiceIdByClassName(static::class);
        return ServiceLocator::getInstance()->get($instanceId);
    }

    /**
     * @return \ANZ\Appointment\Model\RecordTable | string
     */
    public function getRecordDataClass(): string
    {
        return RecordTable::class;
    }

    /**
     * @return \ANZ\Appointment\Services\OneC\Reader
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getReaderService(): Reader
    {
        return $this->serviceLocator->get(static::getServiceIdByClassName(Reader::class));
    }

    /**
     * @return \ANZ\Appointment\Services\OneC\Writer
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getWriterService(): Writer
    {
        return $this->serviceLocator->get(static::getServiceIdByClassName(Writer::class));
    }

    /**
     * @return \ANZ\Appointment\Services\Message\SmsService
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getSmsService(): SmsService
    {
        return $this->serviceLocator->get(static::getServiceIdByClassName(SmsService::class));
    }

    /**
     * @return \ANZ\Appointment\Services\Message\MailerService
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getMailerService(): MailerService
    {
        return $this->serviceLocator->get(static::getServiceIdByClassName(MailerService::class));
    }

    /**
     * Returns service identifier in ServiceLocator by the provided class name
     * For example, \ANZ\Appointment\Services\Container -> anz.appointment.services.container
     * @param string $className
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function getServiceIdByClassName(string $className): string
    {
        $words = explode('\\', $className);
        $serviceId = '';
        foreach ($words as $word)
        {
            $word = lcfirst($word);
            if (!empty($serviceId))
            {
                $serviceId .= '.';
            }
            $serviceId .= $word;
        }

        if (empty($serviceId))
        {
            throw new ArgumentException(Loc::getMessage("ANZ_APPOINTMENT_CONTAINER_CLASSNAME_ERROR"));
        }

        return $serviceId;
    }
}