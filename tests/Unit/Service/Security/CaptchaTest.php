<?php

namespace ANZ\Appointment\Tests\Unit\Service\Security;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Core\Exception\PublicRequestException;
use ANZ\Appointment\Service\Security\Appointment\BookingSession;
use ANZ\Appointment\Service\Security\Captcha\BitrixCaptcha;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class CaptchaTest extends TestCase
{
    private array $options;
    private array $instances;
    private mixed $application;
    private mixed $bookings;

    protected function setUp(): void
    {
        $this->options = (new ReflectionProperty(Option::class, 'values'))->getValue();
        $this->instances = (new ReflectionProperty(Configuration::class, 'instances'))->getValue();
        (new ReflectionProperty(Configuration::class, 'instances'))->setValue(null, []);
        $this->application = $GLOBALS['APPLICATION'] ?? null;
        $this->bookings = Application::getInstance()->getSession()->get('anz_appointment_bookings');
    }

    protected function tearDown(): void
    {
        (new ReflectionProperty(Option::class, 'values'))->setValue(null, $this->options);
        (new ReflectionProperty(Configuration::class, 'instances'))->setValue(null, $this->instances);
        $GLOBALS['APPLICATION'] = $this->application;
        Application::getInstance()->getSession()->set('anz_appointment_bookings', $this->bookings);
    }

    public function testCaptchaIsRequiredByDefault(): void
    {
        $this->expectException(PublicRequestException::class);
        (new BitrixCaptcha())->assertValid('', '');
    }

    public function testDisabledCaptchaDoesNotRequireBitrixOrUserInput(): void
    {
        Option::set('anz.appointment', 'appointment_use_captcha', 'N');
        $GLOBALS['APPLICATION'] = null;
        (new BitrixCaptcha())->assertValid('', '');
        $bookingSession = new BookingSession();
        $bookingSession->rememberBooking(['uid' => 'captcha-disabled']);
        $bookingSession->assertCanConfirm('captcha-disabled');
        self::assertArrayNotHasKey('captchaVerified', $bookingSession->get('captcha-disabled'));
    }

    public function testDisabledCaptchaStillRequiresExistingBooking(): void
    {
        Option::set('anz.appointment', 'appointment_use_captcha', 'N');
        $this->expectException(PublicRequestException::class);
        (new BookingSession())->assertCanConfirm('missing-booking');
    }

    public function testDisabledCaptchaStillRejectsExpiredBooking(): void
    {
        Option::set('anz.appointment', 'appointment_use_captcha', 'N');
        Application::getInstance()->getSession()->set('anz_appointment_bookings', [
            'expired' => ['uid' => 'expired', 'createdAt' => time() - 901],
        ]);
        $this->expectException(PublicRequestException::class);
        (new BookingSession())->assertCanConfirm('expired');
    }

    public function testEnabledCaptchaChecksAnswer(): void
    {
        $GLOBALS['APPLICATION'] = new class {
            public function CaptchaCheckCode(string $word, string $sid): bool
            {
                return $sid === 'challenge' && $word === 'correct';
            }
        };
        (new BitrixCaptcha())->assertValid('challenge', 'correct');
        $this->expectException(PublicRequestException::class);
        (new BitrixCaptcha())->assertValid('challenge', 'incorrect');
    }

    public function testDisabledCaptchaStillRejectsCompletedBooking(): void
    {
        Option::set('anz.appointment', 'appointment_use_captcha', 'N');
        $bookingSession = new BookingSession();
        $bookingSession->rememberBooking(['uid' => 'completed']);
        $bookingSession->markAppointmentCreated('completed', []);
        $this->expectException(PublicRequestException::class);
        $bookingSession->assertCanConfirm('completed');
    }

    public function testUnexpectedSettingDoesNotDisableCaptcha(): void
    {
        Option::set('anz.appointment', 'appointment_use_captcha', '');
        $this->expectException(PublicRequestException::class);
        (new BitrixCaptcha())->assertValid('', '');
    }

    public function testReenabledCaptchaRejectsPreviouslyUnverifiedBooking(): void
    {
        Option::set('anz.appointment', 'appointment_use_captcha', 'N');
        $bookingSession = new BookingSession();
        $bookingSession->rememberBooking(['uid' => 'reenabled']);
        $bookingSession->assertCanConfirm('reenabled');
        Option::set('anz.appointment', 'appointment_use_captcha', 'Y');
        (new ReflectionProperty(Configuration::class, 'instances'))->setValue(null, []);
        $this->expectException(PublicRequestException::class);
        $bookingSession->assertCanConfirm('reenabled');
    }
}
