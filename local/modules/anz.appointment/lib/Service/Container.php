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

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Config\Constants;
use ANZ\Appointment\Integration\UmcSdk\Builder\ExchangeClient;
use ANZ\Appointment\Integration\UmcSdk\Factory;
use ANZ\Appointment\Integration\UmcSdk\Service\Exchange\Soap;
use ANZ\Appointment\Internals\Control\ServiceManager;
use ANZ\Appointment\Internals\Model\RecordTable;
use ANZ\Appointment\Service\OneC\Exchange;
use ANZ\Appointment\Service\OneC\FtpDataReader;
use ANZ\Appointment\Service\Provider\ExchangeDataProvider;
use ANZ\BitUmc\SDK\Core\Contract\Service\IExchangeService;
use ANZ\BitUmc\SDK\Core\Dictionary\ClientScope;
use ANZ\BitUmc\SDK\Core\Trait\Singleton;
use ANZ\BitUmc\SDK\Service\XmlParser;
use Bitrix\Main\Config\Option;
use Bitrix\Main\DI\ServiceLocator;
use ANZ\Appointment\Service\Message\Mailer;
use ANZ\Appointment\Service\Message\Sms;
use Bitrix\Main\Localization\Loc;
use Exception;

/**
 * Class Container
 * @package ANZ\Appointment\Service
 * @method static Container getInstance()
 */
class Container
{
    use Singleton;

    /**
     * @throws \Exception | \Psr\Container\NotFoundExceptionInterface
     */
    public function getSdkExchangeService(): Soap
    {
        $identifier = static::getIdentifierByClassName(IExchangeService::class);

        if(!ServiceLocator::getInstance()->has($identifier))
        {
            $login      = Option::get(ServiceManager::getModuleId(), Constants::OPTION_KEY_API_WS_LOGIN);
            $password   = Option::get(ServiceManager::getModuleId(), Constants::OPTION_KEY_API_WS_PASSWORD);

            if (empty($login) || empty($password)){
                throw new Exception(Loc::getMessage("ANZ_APPOINTMENT_SOAP_AUTH_ERROR"));
            }

            $url = trim(Option::get(ServiceManager::getModuleId(), Constants::OPTION_KEY_API_WS_URL));

            if (empty($url)){
                throw new Exception(Loc::getMessage("ANZ_APPOINTMENT_SOAP_URL_ERROR"));
            }

            $client = ExchangeClient::init()
                ->setLogin($login)
                ->setPassword($password)
                ->setFullBaseUrl($url)
                ->setScope(ClientScope::WEB_SERVICE)
                ->build();

            $exchangeService = (new Factory\Exchange($client))->create();
            ServiceLocator::getInstance()->addInstance($identifier, $exchangeService);
        }

        return ServiceLocator::getInstance()->get($identifier);
    }

    /**
     * @return \ANZ\Appointment\Internals\Contract\Service\IExchangeService
     * @throws \Exception
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getExchangeService(): \ANZ\Appointment\Internals\Contract\Service\IExchangeService
    {
        $useDemoMode  = Configuration::getInstance()->isDemoModeOn();

        if ($useDemoMode)
        {
            $serviceClass = Exchange::class;
        }
        else
        {
            $useFtpMode   = Configuration::getInstance()->isFtpModeOn();
            $serviceClass = $useFtpMode ? FtpDataReader::class : Exchange::class;
        }
        $identifier = static::getIdentifierByClassName($serviceClass);

        if(!ServiceLocator::getInstance()->has($identifier))
        {
            ServiceLocator::getInstance()->addInstance($identifier, new $serviceClass());
        }

        return ServiceLocator::getInstance()->get($identifier);
    }

    /**
     * @return \ANZ\Appointment\Service\Message\Sms
     * @throws \Exception
     * @throws \Psr\Container\NotFoundExceptionInterface
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
     * @throws \Psr\Container\NotFoundExceptionInterface
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
     * @return \ANZ\Appointment\Service\Provider\ExchangeDataProvider
     * @throws \Exception
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getExchangeDataProvider(): ExchangeDataProvider
    {
        $identifier = static::getIdentifierByClassName(ExchangeDataProvider::class);

        if(!ServiceLocator::getInstance()->has($identifier))
        {
            ServiceLocator::getInstance()->addInstance($identifier, new ExchangeDataProvider);
        }

        return ServiceLocator::getInstance()->get($identifier);
    }

    /**
     * @return \ANZ\BitUmc\SDK\Service\XmlParser
     * @throws \Psr\Container\NotFoundExceptionInterface
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

    /**
     * @return \ANZ\Appointment\Internals\Model\RecordTable|string
     */
    public function getRecordDataClass(): RecordTable | string
    {
        return RecordTable::class;
    }

    /**
     * @return \ANZ\Appointment\Service\Converter\Order
     * @throws \Exception
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getOrderConverter(): Converter\Order
    {
        $identifier = static::getIdentifierByClassName(Converter\Order::class);

        if(!ServiceLocator::getInstance()->has($identifier))
        {
            ServiceLocator::getInstance()->addInstance($identifier, new Converter\Order);
        }

        return ServiceLocator::getInstance()->get($identifier);
    }
}