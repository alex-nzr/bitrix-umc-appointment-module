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
namespace ANZ\Appointment\Service;

use ANZ\Appointment\Internals\Model\RecordTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\DI\ServiceLocator;
use ANZ\Appointment\Service\Message\Mailer;
use ANZ\Appointment\Service\Message\Sms;
use ANZ\Appointment\Service\OneC\Reader;
use ANZ\Appointment\Service\OneC\Writer;

/**
 * Class Container
 * @package ANZ\Appointment\Service
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
     * @return \ANZ\Appointment\Internals\Model\RecordTable | string
     */
    public function getRecordDataClass(): string
    {
        return RecordTable::class;
    }

    /**
     * @return \ANZ\Appointment\Service\OneC\Reader
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getReaderService(): Reader
    {
        return $this->serviceLocator->get(static::getServiceIdByClassName(Reader::class));
    }

    /**
     * @return \ANZ\Appointment\Service\OneC\Writer
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getWriterService(): Writer
    {
        return $this->serviceLocator->get(static::getServiceIdByClassName(Writer::class));
    }

    /**
     * @return \ANZ\Appointment\Service\Message\Sms
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getSmsService(): Sms
    {
        return $this->serviceLocator->get(static::getServiceIdByClassName(Sms::class));
    }

    /**
     * @return \ANZ\Appointment\Service\Message\Mailer
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getMailerService(): Mailer
    {
        return $this->serviceLocator->get(static::getServiceIdByClassName(Mailer::class));
    }

    /**
     * Returns service identifier in ServiceLocator by the provided class name
     * For example, \ANZ\Appointment\Service\Container -> anz.appointment.service.container
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
            if ($word === 'ANZ')
            {
                $word = strtolower($word);
            }
            else
            {
                $word = lcfirst($word);
                if (!empty($serviceId))
                {
                    $serviceId .= '.';
                }
            }
            $serviceId .= $word;
        }

        if (empty($serviceId))
        {
            throw new ArgumentException('className should be a valid string');
        }

        return $serviceId;
    }
}