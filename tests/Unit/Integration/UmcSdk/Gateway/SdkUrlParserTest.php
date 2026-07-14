<?php

namespace ANZ\Appointment\Tests\Unit\Integration\UmcSdk\Gateway;

use ANZ\Appointment\Dto\ScheduleItemDto;
use ANZ\Appointment\Integration\UmcSdk\Mapper\SdkResponseToDto;
use ANZ\Appointment\Integration\UmcSdk\Gateway\Sdk;
use ANZ\Appointment\Integration\UmcSdk\Validator\ResponseValidator;
use ANZ\BitUmc\SDK\Transport\Protocol;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class SdkUrlParserTest extends TestCase
{
    public function testParserConvertsFullWsdlUrlToConnectionOptionsParts(): void
    {
        $reflection = new ReflectionClass(Sdk::class);
        $sdk = $reflection->newInstanceWithoutConstructor();
        $method = $reflection->getMethod('parsePublicationUrl');
        $method->setAccessible(true);

        $result = $method->invoke($sdk, 'http://example.local:3500/chelbit_umc/ws/Integration?wsdl');

        $this->assertSame(Protocol::HTTP, $result['protocol']);
        $this->assertSame('example.local:3500', $result['host']);
        $this->assertSame('chelbit_umc', $result['baseName']);
    }

    public function testParserSupportsNestedBaseNameBeforeWsSegment(): void
    {
        $reflection = new ReflectionClass(Sdk::class);
        $sdk = $reflection->newInstanceWithoutConstructor();
        $method = $reflection->getMethod('parsePublicationUrl');
        $method->setAccessible(true);

        $result = $method->invoke($sdk, 'https://example.local/published/base/ws/Integration?wsdl');

        $this->assertSame(Protocol::HTTPS, $result['protocol']);
        $this->assertSame('example.local', $result['host']);
        $this->assertSame('published/base', $result['baseName']);
    }

    public function testDemoScheduleIsMappedToDtoStructure(): void
    {
        $reflection = new ReflectionClass(Sdk::class);
        $sdk = $reflection->newInstanceWithoutConstructor();

        $this->setProperty($reflection, $sdk, 'demoMode', true);
        $this->setProperty($reflection, $sdk, 'responseMapper', new SdkResponseToDto());
        $this->setProperty($reflection, $sdk, 'responseValidator', new ResponseValidator());
        $this->setProperty($reflection, $sdk, 'demoData', [
            'schedule' => [
                'clinic-1' => [
                    'specialty-1' => [
                        'employee-1' => [
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
                                        ],
                                    ],
                                ],
                                'busy' => [],
                                'freeFormatted' => [],
                            ],
                        ],
                        'employee-2' => [
                            'specialtyName' => 'Therapist',
                            'employeeName' => 'Petrov Petr Petrovich',
                            'durationInSeconds' => 900,
                            'timetable' => [
                                'free' => [
                                    '03-07-2026' => [
                                        [
                                            'typeOfTimeUid' => 'type-2',
                                            'date' => '2026-07-03',
                                            'timeBegin' => '2026-07-03T09:00:00',
                                            'timeEnd' => '2026-07-03T09:15:00',
                                            'formattedDate' => '03-07-2026',
                                            'formattedTimeBegin' => '09:00',
                                            'formattedTimeEnd' => '09:15',
                                        ],
                                    ],
                                ],
                                'busy' => [],
                                'freeFormatted' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $schedule = $sdk->getSchedule(14, 'clinic-1', ['employee-1']);
        $scheduleItem = $schedule['clinic-1']['specialty-1']['employee-1'];

        $this->assertInstanceOf(ScheduleItemDto::class, $scheduleItem);
        $this->assertSame('2026-07-03T09:00:00', $scheduleItem->timeslots['free']['03-07-2026'][0]->timeBegin);
        $this->assertArrayNotHasKey('employee-2', $schedule['clinic-1']['specialty-1']);
    }

    private function setProperty(ReflectionClass $reflection, object $object, string $name, mixed $value): void
    {
        $property = $reflection->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
}
