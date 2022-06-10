<?php
namespace FirstBit\Appointment\Services;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Localization\Loc;
use FirstBit\Appointment\Model\RecordTable;

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
     * @return \FirstBit\Appointment\Services\OneCReader
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getReaderService(): OneCReader
    {
        return $this->serviceLocator->get(static::getServiceIdByClassName(OneCReader::class));
    }

    /**
     * @return \FirstBit\Appointment\Services\OneCWriter
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getWriterService(): OneCWriter
    {
        return $this->serviceLocator->get(static::getServiceIdByClassName(OneCWriter::class));
    }

    /**
     * @return \FirstBit\Appointment\Services\SmsService
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getSmsService(): SmsService
    {
        return $this->serviceLocator->get(static::getServiceIdByClassName(SmsService::class));
    }

    /**
     * @return \FirstBit\Appointment\Services\MailerService
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