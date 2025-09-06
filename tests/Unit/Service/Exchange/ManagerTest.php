<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 06.09.2025
 * ==================================================
*/

namespace ANZ\Appointment\Tests\Unit\Service\Exchange;

use ANZ\Appointment\Core\Exception\ExchangeManagerException;
use ANZ\Appointment\Dto\ClinicDto;
use ANZ\Appointment\Service\Exchange\Manager;
use ANZ\Appointment\Tests\Unit\Integration\UmcSdk\Gateway\FakeSdk;
use ANZ\Appointment\Tests\Unit\Model\FakeRecordTable;
use ANZ\Appointment\Tests\Unit\Repository\UMC\FakeAppointmentRepository;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ManagerTest extends TestCase
{
    /**
     * @throws ExchangeManagerException
     */
    public function testGetClinicsSuccess(): void
    {
        $svc  = new Manager(
            new FakeSdk(), new FakeAppointmentRepository(FakeRecordTable::getEntity())
        );

        /** @var ClinicDto $clinic */
        $clinic = current($svc->getClinics());

        $this->assertTrue($clinic instanceof ClinicDto);
        $this->assertSame('uniq-uid-12345', $clinic->uid);
        $this->assertSame('testClinic', $clinic->name);
    }

    public function testWrappingGatewayErrors(): void
    {
        $this->expectException(ExchangeManagerException::class);

        $gateway = new FakeSdk();
        $gateway->toThrow = new RuntimeException('UMC temporarily unavailable');

        $svc  = new Manager(
            $gateway, new FakeAppointmentRepository(FakeRecordTable::getEntity())
        );

        $svc->getClinics();
    }
}