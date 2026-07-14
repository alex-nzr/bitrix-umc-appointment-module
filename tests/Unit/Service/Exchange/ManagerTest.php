<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 06.09.2025
 * ==================================================
*/

namespace ANZ\Appointment\Tests\Unit\Service\Exchange;

use ANZ\Appointment\Core\Exception\ExchangeManagerException;
use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Config\Constants;
use ANZ\Appointment\Dto\BookingDto;
use ANZ\Appointment\Dto\ClinicDto;
use ANZ\Appointment\Dto\EmployeeDto;
use ANZ\Appointment\Dto\ServiceDto;
use ANZ\Appointment\Service\Security\AppointmentPayloadGuard;
use ANZ\Appointment\Service\Exchange\Manager;
use ANZ\Appointment\Tests\Unit\Integration\UmcSdk\Gateway\FakeSdk;
use ANZ\Appointment\Tests\Unit\Model\FakeRecordTable;
use ANZ\Appointment\Tests\Unit\Repository\UMC\FakeAppointmentRepository;
use ANZ\Appointment\Tests\Unit\Repository\UMC\SpyAppointmentRepository;
use Bitrix\Main\Config\Option;
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

        $booking = $svc->sendBooking('clinic-1', 'employee-1', '2026-03-26 10:00:00', 1800);

        $this->assertTrue($booking instanceof BookingDto);
        $this->assertSame('clinic-1', $booking->clinicUid);
        $this->assertSame('employee-1', $booking->employeeUid);
        $this->assertSame(1800, $booking->serviceDuration);
    }

    /**
     * @throws ExchangeManagerException
     */
    public function testSendBookingAllowsEmployeeWithoutClinic(): void
    {
        $gateway = new FakeSdk();
        $gateway->employees = [
            new EmployeeDto(
                'employee-1',
                'Ivan',
                'Ivanov',
                'Ivanovich',
                'Ivanov Ivan Ivanovich',
                '',
                '',
                '',
                '',
                'Therapist',
                'specialty-1'
            )
        ];

        $svc = new Manager($gateway, new FakeAppointmentRepository(FakeRecordTable::getEntity()));

        $booking = $svc->sendBooking('clinic-1', 'employee-1', '2026-03-26 10:00:00', 1800);

        $this->assertInstanceOf(BookingDto::class, $booking);
    }

    /**
     * @throws ExchangeManagerException
     */
    public function testSendBookingAllowsEmployeeWithDifferentDictionaryClinicWhenSlotExists(): void
    {
        $gateway = new FakeSdk();
        $gateway->employees = [
            new EmployeeDto(
                'employee-1',
                'Ivan',
                'Ivanov',
                'Ivanovich',
                'Ivanov Ivan Ivanovich',
                'another-clinic',
                '',
                '',
                '',
                'Therapist',
                'specialty-1'
            )
        ];

        $svc = new Manager($gateway, new FakeAppointmentRepository(FakeRecordTable::getEntity()));

        $booking = $svc->sendBooking('clinic-1', 'employee-1', '2026-03-26 10:00:00', 1800);

        $this->assertInstanceOf(BookingDto::class, $booking);
        $this->assertSame('clinic-1', $booking->clinicUid);
    }

    /**
     * @throws ExchangeManagerException
     */
    public function testSendBookingSkipsSlotAvailabilityCheckInDemoMode(): void
    {
        Option::set(Configuration::getModuleId(), Constants::OPTION_KEY_DEMO_MODE, 'Y');
        $this->clearConfigurationOptionCache();

        $svc = new Manager(new FakeSdk(), new FakeAppointmentRepository(FakeRecordTable::getEntity()));

        $booking = $svc->sendBooking('clinic-1', 'employee-1', '2026-04-09 11:00:00', 3600);

        $this->assertInstanceOf(BookingDto::class, $booking);
        $this->assertSame('clinic-1', $booking->clinicUid);

        Option::set(Configuration::getModuleId(), Constants::OPTION_KEY_DEMO_MODE, 'N');
        $this->clearConfigurationOptionCache();
    }

    public function testSendAppointmentAllowsEmployeeWithoutServiceRelations(): void
    {
        Option::set(Configuration::getModuleId(), Constants::OPTION_KEY_EXCHANGE_USE_SERVICES, 'Y');
        $this->clearConfigurationOptionCache();

        $gateway = new FakeSdk();
        $gateway->employees = [
            new EmployeeDto(
                'employee-1',
                'Ivan',
                'Ivanov',
                'Ivanovich',
                'Ivanov Ivan Ivanovich',
                'clinic-1',
                '',
                '',
                '',
                'Therapist',
                'specialty-1',
                []
            )
        ];
        $gateway->services = [new ServiceDto('service-1', 'Consultation', 'service', 'ART-1', 1000, 1800, 'pcs', '')];

        $svc = new Manager($gateway, new FakeAppointmentRepository(FakeRecordTable::getEntity()));
        $booking = [
            'uid' => 'booking-1',
            'clinicUid' => 'clinic-1',
            'employeeUid' => 'employee-1',
            'timeBegin' => '2026-03-26 10:00:00',
            'serviceDuration' => 1800,
        ];

        (new AppointmentPayloadGuard())->assertAppointmentPayload($svc, [
            'bookingUid' => $booking['uid'],
            'clinicUid' => 'clinic-1',
            'employeeUid' => 'employee-1',
            'serviceUid' => 'service-1',
            'serviceDuration' => 1800,
            'timeBegin' => '2026-03-26 10:00:00',
            'phone' => '+79990000000',
            'surname' => 'Ivanov',
            'name' => 'Ivan',
            'middleName' => 'Ivanovich',
            'email' => 'test@example.com',
            'comment' => 'Test comment',
        ], $booking);

        $this->assertTrue(true);
    }

    public function testSendAppointmentDoesNotValidateServiceRelationsWhenServicesDisabled(): void
    {
        Option::set(Configuration::getModuleId(), Constants::OPTION_KEY_EXCHANGE_USE_SERVICES, 'N');
        $this->clearConfigurationOptionCache();

        $gateway = new FakeSdk();
        $svc = new Manager($gateway, new FakeAppointmentRepository(FakeRecordTable::getEntity()));
        $booking = [
            'uid' => 'booking-1',
            'clinicUid' => 'clinic-1',
            'employeeUid' => 'employee-1',
            'timeBegin' => '2026-03-26 10:00:00',
            'serviceDuration' => 1800,
        ];

        (new AppointmentPayloadGuard())->assertAppointmentPayload($svc, [
            'bookingUid' => $booking['uid'],
            'clinicUid' => 'clinic-1',
            'employeeUid' => 'employee-1',
            'serviceUid' => 'unknown-service',
            'serviceDuration' => 1800,
            'timeBegin' => '2026-03-26 10:00:00',
            'phone' => '+79990000000',
            'surname' => 'Ivanov',
            'name' => 'Ivan',
            'middleName' => 'Ivanovich',
            'email' => 'test@example.com',
            'comment' => 'Test comment',
        ], $booking);

        $this->assertTrue(true);
    }

    /**
     * @throws ExchangeManagerException
     */
    public function testDeleteAppointmentDeletesRecordFromRepositoryAfterGatewayCall(): void
    {
        $repository = new SpyAppointmentRepository(FakeRecordTable::getEntity());
        $repository->entityByPrimary = new class extends EntityObject {
            public function getXmlId(): string
            {
                return 'booking-uid-55';
            }
        };
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

    private function clearConfigurationOptionCache(): void
    {
        $property = new \ReflectionProperty(Configuration::class, 'optionCache');
        $property->setAccessible(true);
        $property->setValue(Configuration::getInstance(), []);
    }
}
