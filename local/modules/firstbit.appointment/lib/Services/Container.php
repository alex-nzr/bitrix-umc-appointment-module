<?php
namespace FirstBit\Appointment\Services;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Localization\Loc;
use FirstBit\Appointment\Model\RecordTable;
use FirstBit\Appointment\Services\Message\MailerService;
use FirstBit\Appointment\Services\Message\SmsService;
use FirstBit\Appointment\Services\OneC\Reader;
use FirstBit\Appointment\Services\OneC\Writer;

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
     * @return \FirstBit\Appointment\Model\RecordTable | string
     */
    public function getRecordDataClass(): string
    {
        return RecordTable::class;
    }

    /**
     * @return \FirstBit\Appointment\Services\OneC\Reader
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getReaderService(): Reader
    {
        return $this->serviceLocator->get(static::getServiceIdByClassName(Reader::class));
    }

    /**
     * @return \FirstBit\Appointment\Services\OneC\Writer
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getWriterService(): Writer
    {
        return $this->serviceLocator->get(static::getServiceIdByClassName(Writer::class));
    }

    /**
     * @return \FirstBit\Appointment\Services\Message\SmsService
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getSmsService(): SmsService
    {
        return $this->serviceLocator->get(static::getServiceIdByClassName(SmsService::class));
    }

    /**
     * @return \FirstBit\Appointment\Services\Message\MailerService
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getMailerService(): MailerService
    {
        return $this->serviceLocator->get(static::getServiceIdByClassName(MailerService::class));
    }

    /**
     * Returns service identifier in ServiceLocator by the provided class name
     * For example, \FirstBit\Appointment\Services\Container -> firstbit.appointment.services.container
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
            throw new ArgumentException(Loc::getMessage("FIRSTBIT_APPOINTMENT_CONTAINER_CLASSNAME_ERROR"));
        }

        return $serviceId;
    }
}