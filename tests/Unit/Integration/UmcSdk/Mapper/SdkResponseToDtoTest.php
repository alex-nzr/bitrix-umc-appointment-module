<?php

namespace ANZ\Appointment\Tests\Unit\Integration\UmcSdk\Mapper;

use ANZ\Appointment\Integration\UmcSdk\Mapper\SdkResponseToDto;
use PHPUnit\Framework\TestCase;

class SdkResponseToDtoTest extends TestCase
{
    public function testItKeepsExtraFieldsInClinic(): void
    {
        $dto = (new SdkResponseToDto())->clinicFromArray([
            'uid' => 'clinic-1',
            'name' => 'Clinic',
            '_extra' => [
                'Подразделения' => [
                    'Подразделение' => [
                        'ГУИД' => 'department-1',
                    ],
                ],
            ],
        ]);

        self::assertSame('department-1', $dto->extra['Подразделения']['Подразделение']['ГУИД']);
    }

    public function testItKeepsExtraFieldsInEmployeeAndEmployeeServices(): void
    {
        $dto = (new SdkResponseToDto())->employeeFromArray([
            'uid' => 'employee-1',
            'name' => 'Ivan',
            'surname' => 'Ivanov',
            'middleName' => 'Ivanovich',
            'fullName' => 'Ivanov Ivan Ivanovich',
            'clinicUid' => 'clinic-1',
            'photo' => '',
            'description' => '',
            'rating' => '',
            'specialtyName' => 'Therapist',
            'specialtyUid' => 'specialty-1',
            'services' => [
                [
                    'uid' => 'service-1',
                    'personalDuration' => 900,
                    '_extra' => [
                        'ВозрастОт' => '18',
                    ],
                ],
            ],
            '_extra' => [
                'Подразделение' => 'department-1',
            ],
        ]);

        self::assertSame('department-1', $dto->extra['Подразделение']);
        self::assertSame('18', $dto->services[0]->extra['ВозрастОт']);
    }

    public function testItKeepsExtraFieldsInService(): void
    {
        $dto = (new SdkResponseToDto())->serviceFromArray([
            'uid' => 'service-1',
            'name' => 'Consultation',
            'typeOfItem' => 'service',
            'artNumber' => 'ART',
            'price' => 1000,
            'duration' => 900,
            'measureUnit' => 'pcs',
            'parent' => '',
            '_extra' => [
                'СкидкаПроцент' => '15',
            ],
        ]);

        self::assertSame('15', $dto->extra['СкидкаПроцент']);
    }

    public function testItKeepsExtraFieldsInScheduleAndTimeSlots(): void
    {
        $dto = (new SdkResponseToDto())->scheduleItemFromArray(
            'clinic-1',
            'specialty-1',
            'employee-1',
            [
                'specialtyName' => 'Therapist',
                'employeeName' => 'Ivanov Ivan Ivanovich',
                'durationInSeconds' => 900,
                'timetable' => [
                    'free' => [
                        '03-07-2026' => [
                            [
                                'typeOfTimeUid' => 'type-1',
                                'date' => '2026-07-03',
                                'timeBegin' => '2026-07-03T09:00:00',
                                'timeEnd' => '2026-07-03T09:15:00',
                                'formattedDate' => '03-07-2026',
                                'formattedTimeBegin' => '09:00',
                                'formattedTimeEnd' => '09:15',
                                '_extra' => [
                                    'КастомноеПолеТаймслота' => 'slot-value',
                                ],
                            ],
                        ],
                    ],
                    'busy' => [],
                    'freeFormatted' => [],
                ],
                '_extra' => [
                    'Подразделение' => 'department-1',
                ],
            ]
        );

        self::assertSame('department-1', $dto->extra['Подразделение']);
        self::assertSame('slot-value', $dto->timeslots['free']['03-07-2026'][0]->extra['КастомноеПолеТаймслота']);
    }
}
