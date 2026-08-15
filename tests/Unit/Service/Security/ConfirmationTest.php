<?php

namespace ANZ\Appointment\Tests\Unit\Service\Security;

use ANZ\Appointment\Core\Exception\ValidatorException;
use ANZ\Appointment\Service\Security\Confirmation;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class ConfirmationTest extends TestCase
{
    public function testPhoneConfirmationNormalizesValidPhoneBeforeSending(): void
    {
        $method = new ReflectionMethod(Confirmation::class, 'resolveTarget');

        self::assertSame(
            '+79990000000',
            $method->invoke(new Confirmation(), 'phone', '8 (999) 000-00-00', '')
        );
    }

    public function testPhoneConfirmationRejectsInvalidPhoneBeforeSending(): void
    {
        $method = new ReflectionMethod(Confirmation::class, 'resolveTarget');

        $this->expectException(ValidatorException::class);
        $method->invoke(new Confirmation(), 'phone', 'not-a-phone', '');
    }
}
