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
use ANZ\Appointment\Service\Xml\FtpDataReader;
use ANZ\Appointment\Service\Xml\XmlParser;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\DI\ServiceLocator;
use ANZ\Appointment\Service\Message\Mailer;
use ANZ\Appointment\Service\Message\Sms;
use ANZ\Appointment\Service\OneC\Reader;
use ANZ\Appointment\Service\OneC\Writer;
use Exception;

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
     * @throws \Exception
     */
    public static function getInstance(): Container
    {
        $identifier = static::getIdentifierByClassName(static::class);
        return ServiceLocator::getInstance()->get($identifier);
    }

    /**
     * @return \ANZ\Appointment\Internals\Model\RecordTable | string
     */
    public function getRecordDataClass(): string
    {
        return RecordTable::class;
    }

    /**
     * @return \ANZ\Appointment\Service\Xml\FtpDataReader
     * @throws \Exception
     */
    public function getFtpDataReader(): FtpDataReader
    {
        $identifier = static::getIdentifierByClassName(FtpDataReader::class);

        if(!ServiceLocator::getInstance()->has($identifier))
        {
            ServiceLocator::getInstance()->addInstance($identifier, new FtpDataReader);
        }

        return ServiceLocator::getInstance()->get($identifier);
    }

    /**
     * @return \ANZ\Appointment\Service\OneC\Reader
     * @throws \Exception
     */
    public function getOneCReader(): Reader
    {
        $identifier = static::getIdentifierByClassName(Reader::class);

        if(!ServiceLocator::getInstance()->has($identifier))
        {
            ServiceLocator::getInstance()->addInstance($identifier, new Reader);
        }

        return ServiceLocator::getInstance()->get($identifier);
    }

    /**
     * @return \ANZ\Appointment\Service\OneC\Writer
     * @throws \Exception
     */
    public function getOneCWriter(): Writer
    {
        $identifier = static::getIdentifierByClassName(Writer::class);

        if(!ServiceLocator::getInstance()->has($identifier))
        {
            ServiceLocator::getInstance()->addInstance($identifier, new Writer);
        }

        return ServiceLocator::getInstance()->get($identifier);
    }

    /**
     * @return \ANZ\Appointment\Service\Message\Sms
     * @throws \Exception
     */
    public function getSmsService(): Sms
    {
        $identifier = static::getIdentifierByClassName(Sms::class);

        if(!ServiceLocator::getInstance()->has($identifier))
        {
            ServiceLocator::getInstance()->addInstance($identifier, new Sms);
        }

        return ServiceLocator::getInstance()->get($identifier);
    }

    /**
     * @return \ANZ\Appointment\Service\Message\Mailer
     * @throws \Exception
     */
    public function getMailerService(): Mailer
    {
        $identifier = static::getIdentifierByClassName(Mailer::class);

        if(!ServiceLocator::getInstance()->has($identifier))
        {
            ServiceLocator::getInstance()->addInstance($identifier, new Mailer);
        }

        return ServiceLocator::getInstance()->get($identifier);
    }

    /**
     * @return \ANZ\Appointment\Service\Xml\XmlParser
     * @throws \Exception
     */
    public function getXmlParser(): XmlParser
    {
        $identifier = static::getIdentifierByClassName(XmlParser::class);

        if(!ServiceLocator::getInstance()->has($identifier))
        {
            ServiceLocator::getInstance()->addInstance($identifier, new XmlParser);
        }

        return ServiceLocator::getInstance()->get($identifier);
    }

    /**
     * @param string $className
     * @param array|null $parameters
     * @return string
     * @throws \Exception
     */
    public static function getIdentifierByClassName(string $className, array $parameters = null): string
    {
        $words = explode('\\', $className);
        $identifier = '';
        foreach ($words as $word)
        {
            if ($word === 'ANZ')
            {
                $identifier .= strtolower($word);
            }
            else
            {
                $identifier .= !empty($identifier) ? '.'.lcfirst($word) : lcfirst($word);
            }
        }

        if (empty($identifier))
        {
            throw new Exception('className should be a valid string');
        }

        if(!empty($parameters))
        {
            $parameters = array_filter($parameters, static function($parameter) {
                return (!empty($parameter) && (is_string($parameter) || is_numeric($parameter)));
            });

            if(!empty($parameters))
            {
                $identifier .= '.' . implode('.', $parameters);
            }
        }

        return $identifier;
    }
}