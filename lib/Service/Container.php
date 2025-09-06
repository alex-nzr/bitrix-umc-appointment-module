<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 10.07.2022
 * ==================================================
*/
namespace ANZ\Appointment\Service;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Core\Exception\ServiceContainerException;
use ANZ\Appointment\Core\Trait\Singleton;
use ANZ\Appointment\Integration\UmcSdk\Cache\CacheProvider;
use ANZ\Appointment\Integration\UmcSdk\Gateway\Sdk;
use ANZ\Appointment\Integration\UmcSdk\Mapper\SdkResponseToDto;
use ANZ\Appointment\Integration\UmcSdk\Validator\ResponseValidator;
use ANZ\Appointment\Model\RecordTable;
use ANZ\Appointment\Repository\UMC\AppointmentRepository;
use ANZ\Appointment\Service\Access\UserPermissions;
use ANZ\Appointment\Service\Exchange\Manager;
use ANZ\Appointment\Service\Message\Mailer;
use ANZ\Appointment\Service\Message\Sms;
use ANZ\Appointment\Service\Security\Encryptor;
use Bitrix\Main\DI\ServiceLocator;
use Throwable;

/**
  * @method static Container getInstance()
 */
class Container
{
    use Singleton;

    /**
     * @throws ServiceContainerException
     */
    public function getExchangeManager(): Manager
    {
        try
        {
            $identifier = static::getIdentifierByClassName(Manager::class);
            if(!ServiceLocator::getInstance()->has($identifier))
            {
                ServiceLocator::getInstance()->addInstance($identifier, new Manager(
                    $this->getSdkGateway(),
                    new AppointmentRepository(RecordTable::getEntity())
                ));
            }
            return ServiceLocator::getInstance()->get($identifier);
        }
        catch(Throwable $e)
        {
            throw new ServiceContainerException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws ServiceContainerException
     */
    public function getSdkGateway(): Sdk
    {
        try
        {
            $identifier = static::getIdentifierByClassName(Sdk::class);

            if(!ServiceLocator::getInstance()->has($identifier))
            {
                ServiceLocator::getInstance()->addInstance($identifier, new Sdk(
                    Configuration::getInstance()->isDemoModeOn(),
                    new SdkResponseToDto,
                    new ResponseValidator,
                    $this->getUmcIntegrationCacheProvider()
                ));
            }

            return ServiceLocator::getInstance()->get($identifier);
        }
        catch(Throwable $e)
        {
            throw new ServiceContainerException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws ServiceContainerException
     */
    public function getUmcIntegrationCacheProvider(): CacheProvider
    {
        try
        {
            $identifier = static::getIdentifierByClassName(CacheProvider::class);
            if(!ServiceLocator::getInstance()->has($identifier))
            {
                ServiceLocator::getInstance()->addInstance($identifier, new CacheProvider);
            }
            return ServiceLocator::getInstance()->get($identifier);
        }
        catch(Throwable $e)
        {
            throw new ServiceContainerException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws ServiceContainerException
     */
    public function getSmsService(): Sms
    {
        try
        {
            $identifier = static::getIdentifierByClassName(Sms::class);
            if(!ServiceLocator::getInstance()->has($identifier))
            {
                ServiceLocator::getInstance()->addInstance($identifier, new Sms);
            }
            return ServiceLocator::getInstance()->get($identifier);
        }
        catch(Throwable $e)
        {
            throw new ServiceContainerException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws ServiceContainerException
     */
    public function getMailerService(): Mailer
    {
        try
        {
            $identifier = static::getIdentifierByClassName(Mailer::class);
            if(!ServiceLocator::getInstance()->has($identifier))
            {
                ServiceLocator::getInstance()->addInstance($identifier, new Mailer);
            }
            return ServiceLocator::getInstance()->get($identifier);
        }
        catch(Throwable $e)
        {
            throw new ServiceContainerException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getRecordDataClass(): RecordTable | string
    {
        return RecordTable::class;
    }

    /**
     * @throws ServiceContainerException
     */
    public function getUserPermissions(): UserPermissions
    {
        try
        {
            $identifier = static::getIdentifierByClassName(UserPermissions::class);
            if(!ServiceLocator::getInstance()->has($identifier))
            {
                ServiceLocator::getInstance()->addInstance($identifier, new UserPermissions);
            }
            return ServiceLocator::getInstance()->get($identifier);
        }
        catch(Throwable $e)
        {
            throw new ServiceContainerException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws ServiceContainerException
     */
    public function getOrderConverter(): Converter\Order
    {
        try
        {
            $identifier = static::getIdentifierByClassName(Converter\Order::class);
            if(!ServiceLocator::getInstance()->has($identifier))
            {
                ServiceLocator::getInstance()->addInstance($identifier, new Converter\Order);
            }
            return ServiceLocator::getInstance()->get($identifier);
        }
        catch(Throwable $e)
        {
            throw new ServiceContainerException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws ServiceContainerException
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
            throw new ServiceContainerException('className should be a valid string');
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
     * @throws ServiceContainerException
     */
    public function getEncryptService(): Encryptor
    {
        try
        {
            $identifier = static::getIdentifierByClassName(Encryptor::class);
            if(!ServiceLocator::getInstance()->has($identifier))
            {
                ServiceLocator::getInstance()->addInstance($identifier, new Encryptor);
            }
            return ServiceLocator::getInstance()->get($identifier);
        }
        catch(Throwable $e)
        {
            throw new ServiceContainerException($e->getMessage(), $e->getCode(), $e);
        }
    }
}