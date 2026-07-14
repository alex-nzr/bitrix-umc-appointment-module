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
use ANZ\Appointment\Integration\UmcSdk\Mapper\SdkRequestFromParams;
use ANZ\Appointment\Integration\UmcSdk\Mapper\SdkResponseToDto;
use ANZ\Appointment\Integration\UmcSdk\Validator\RequestValidator;
use ANZ\Appointment\Integration\UmcSdk\Validator\ResponseValidator;
use ANZ\Appointment\Model\RecordTable;
use ANZ\Appointment\Repository\UMC\AppointmentRepository;
use ANZ\Appointment\Service\Access\UserPermissions;
use ANZ\Appointment\Service\Exchange\Manager;
use ANZ\Appointment\Service\Message\Mailer;
use ANZ\Appointment\Service\Message\Sms;
use ANZ\Appointment\Service\Security\Confirmation;
use ANZ\Appointment\Service\Security\AppointmentAccess;
use ANZ\Appointment\Service\Security\AppointmentPayloadGuard;
use ANZ\Appointment\Service\Security\BookingSession;
use ANZ\Appointment\Service\Security\Encryptor;
use ANZ\Appointment\Service\Security\OneCUrlGuard;
use ANZ\Appointment\Service\Security\RateLimiter;
use ANZ\Appointment\Service\Security\RateLimitPolicy;
use ANZ\Appointment\Service\Security\SecurityLogger;
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
        return $this->getShared(Manager::class, fn() => new Manager(
            $this->getSdkGateway(),
            new AppointmentRepository(RecordTable::getEntity())
        ));
    }

    /**
     * @throws ServiceContainerException
     */
    public function getSdkGateway(): Sdk
    {
        return $this->getShared(Sdk::class, fn() => new Sdk(
            Configuration::getInstance()->isDemoModeOn(),
            new SdkResponseToDto,
            new SdkRequestFromParams,
            new ResponseValidator,
            new RequestValidator,
            $this->getUmcIntegrationCacheProvider()
        ));
    }

    /**
     * @throws ServiceContainerException
     */
    public function getUmcIntegrationCacheProvider(): CacheProvider
    {
        return $this->getShared(CacheProvider::class, fn() => new CacheProvider);
    }

    /**
     * @throws ServiceContainerException
     */
    public function getSmsService(): Sms
    {
        return $this->getShared(Sms::class, fn() => new Sms);
    }

    /**
     * @throws ServiceContainerException
     */
    public function getMailerService(): Mailer
    {
        return $this->getShared(Mailer::class, fn() => new Mailer);
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
        return $this->getShared(UserPermissions::class, fn() => new UserPermissions);
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
        return $this->getShared(Encryptor::class, fn() => new Encryptor);
    }

    /**
     * @throws \ANZ\Appointment\Core\Exception\ServiceContainerException
     */
    public function getConfirmationService(): Confirmation
    {
        return $this->getShared(Confirmation::class, fn() => new Confirmation);
    }

    /**
     * @throws ServiceContainerException
     */
    public function getSecurityLogger(): SecurityLogger
    {
        return $this->getShared(SecurityLogger::class, fn() => new SecurityLogger());
    }

    /**
     * @throws ServiceContainerException
     */
    public function getRateLimiter(): RateLimiter
    {
        return $this->getShared(RateLimiter::class, fn() => new RateLimiter());
    }

    /**
     * @throws ServiceContainerException
     */
    public function getRateLimitPolicy(): RateLimitPolicy
    {
        return $this->getShared(RateLimitPolicy::class, fn() => new RateLimitPolicy($this->getRateLimiter()));
    }

    /**
     * @throws ServiceContainerException
     */
    public function getOneCUrlGuard(): OneCUrlGuard
    {
        return $this->getShared(OneCUrlGuard::class, fn() => new OneCUrlGuard());
    }

    /**
     * @throws ServiceContainerException
     */
    public function getBookingSession(): BookingSession
    {
        return $this->getShared(BookingSession::class, fn() => new BookingSession());
    }

    /**
     * @throws ServiceContainerException
     */
    public function getAppointmentAccess(): AppointmentAccess
    {
        return $this->getShared(AppointmentAccess::class, fn() => new AppointmentAccess($this->getBookingSession()));
    }

    /**
     * @throws ServiceContainerException
     */
    public function getAppointmentPayloadGuard(): AppointmentPayloadGuard
    {
        return $this->getShared(AppointmentPayloadGuard::class, fn() => new AppointmentPayloadGuard());
    }

    /**
     * @throws ServiceContainerException
     */
    private function getShared(string $className, callable $factory): mixed
    {
        try
        {
            $identifier = static::getIdentifierByClassName($className);
            if(!ServiceLocator::getInstance()->has($identifier))
            {
                ServiceLocator::getInstance()->addInstance($identifier, $factory());
            }
            return ServiceLocator::getInstance()->get($identifier);
        }
        catch(Throwable $e)
        {
            throw new ServiceContainerException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
