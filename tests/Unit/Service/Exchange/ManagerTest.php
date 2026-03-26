<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 06.09.2025
 * ==================================================
*/

namespace ANZ\Appointment\Tests\Unit\Service\Exchange;

use ANZ\Appointment\Core\Exception\ExchangeManagerException;
use ANZ\Appointment\Dto\BookingDto;
use ANZ\Appointment\Dto\ClinicDto;
use ANZ\Appointment\Dto\EmployeeDto;
use ANZ\Appointment\Service\Exchange\Manager;
use ANZ\Appointment\Tests\Unit\Integration\UmcSdk\Gateway\FakeSdk;
use ANZ\Appointment\Tests\Unit\Model\FakeRecordTable;
use ANZ\Appointment\Tests\Unit\Repository\UMC\FakeAppointmentRepository;
use ANZ\Appointment\Tests\Unit\Repository\UMC\SpyAppointmentRepository;
use Bitrix\Main\ORM\Objectify\EntityObject;
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

    /**
     * @throws ExchangeManagerException
     */
    public function testGetEmployeesReturnsGatewayData(): void
    {
        $svc  = new Manager(
            new FakeSdk(), new FakeAppointmentRepository(FakeRecordTable::getEntity())
        );

        /** @var EmployeeDto $employee */
        $employee = current($svc->getEmployees());

        $this->assertTrue($employee instanceof EmployeeDto);
        $this->assertSame('employee-1', $employee->uid);
        $this->assertSame('Ivan', $employee->name);
    }

    /**
     * @throws ExchangeManagerException
     */
    public function testSendBookingReturnsGatewayBookingDto(): void
    {
        $svc  = new Manager(
            new FakeSdk(), new FakeAppointmentRepository(FakeRecordTable::getEntity())
        );

        $booking = $svc->sendBooking('clinic-1', 'employee-1', '2026-03-26 10:00:00', 30);

        $this->assertTrue($booking instanceof BookingDto);
        $this->assertSame('clinic-1', $booking->clinicUid);
        $this->assertSame('employee-1', $booking->employeeUid);
        $this->assertSame(30, $booking->serviceDuration);
    }

    /**
     * @throws ExchangeManagerException
     */
    public function testDeleteAppointmentDeletesRecordFromRepositoryAfterGatewayCall(): void
    {
        $repository = new SpyAppointmentRepository(FakeRecordTable::getEntity());
        $svc = new Manager(new FakeSdk(), $repository);

        $result = $svc->deleteAppointment(55, 'booking-uid-55');

        $this->assertTrue($result);
        $this->assertSame([55], $repository->deletedPrimaryKeys);
    }

    /**
     * @throws ExchangeManagerException
     */
    public function testUpdateAppointmentStatusSavesEntityWhenRepositoryReturnsObject(): void
    {
        $gateway = new FakeSdk();
        $gateway->statusCode = '200';
        $gateway->statusName = 'Confirmed';

        $repository = new SpyAppointmentRepository(FakeRecordTable::getEntity());
        $repository->entityByPrimary = new class extends EntityObject {
            public function setStatus_1c(string $value): static
            {
                return $this->set('STATUS_1C', $value);
            }
        };
        $repository->entityByPrimary->setId(77);

        $svc = new Manager($gateway, $repository);

        $dto = $svc->updateAppointmentStatus(77, 'booking-uid-77');

        $this->assertSame('200', $dto->code);
        $this->assertSame('Confirmed', $dto->name);
        $this->assertCount(1, $repository->savedEntities);
        $this->assertSame('Confirmed', $repository->savedEntities[0]->get('STATUS_1C'));
    }
}
