<?php

namespace ANZ\Appointment\Tests\Unit\Integration\UmcSdk\Gateway;

use ANZ\Appointment\Integration\UmcSdk\Gateway\Sdk;
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
}
