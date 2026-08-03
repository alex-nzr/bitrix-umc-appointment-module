<?php

namespace ANZ\Appointment\Tests\Unit\Service\Security;

use ANZ\Appointment\Service\Security\Encryptor;
use Bitrix\Main\Config\Configuration;
use Bitrix\Main\Security\Cipher;
use PHPUnit\Framework\TestCase;

class EncryptorTest extends TestCase
{
    protected function setUp(): void
    {
        Configuration::setValue('crypto', ['crypto_key' => 'test-master-key']);
    }

    public function testEncryptsAndDecryptsValueWithCurrentFormat(): void
    {
        $encryptor = new Encryptor();
        $encrypted = $encryptor->encrypt('secret-value');

        self::assertStringStartsWith('v2:', $encrypted);
        self::assertNotSame('secret-value', $encrypted);
        self::assertSame('secret-value', $encryptor->decrypt($encrypted));
    }

    public function testEncryptsSameValueToDifferentCiphertexts(): void
    {
        $encryptor = new Encryptor();

        self::assertNotSame($encryptor->encrypt('secret-value'), $encryptor->encrypt('secret-value'));
    }

    public function testKeepsEmptyValueUnchanged(): void
    {
        $encryptor = new Encryptor();

        self::assertSame('', $encryptor->encrypt(''));
        self::assertSame('', $encryptor->decrypt(''));
    }

    public function testRejectsInvalidCurrentValue(): void
    {
        self::assertSame('', (new Encryptor())->decrypt('v2:not-base64'));
    }

    public function testRejectsLegacyValue(): void
    {
        $value = 'legacy-secret';
        $iv = random_bytes(15);
        $encrypted = openssl_encrypt($value, 'AES-256-CBC', 'anz.appointment', OPENSSL_RAW_DATA, str_pad($iv, 16, "\0"));

        self::assertIsString($encrypted);
        self::assertSame('', (new Encryptor())->decrypt(base64_encode($iv . $encrypted)));
    }

    public function testDecryptsV2ValueProducedDirectlyByBitrixCipher(): void
    {
        $value = 'migrated-secret';
        $cryptoKey = 'test-master-key';
        $migratedValue = 'v2:' . base64_encode((new Cipher())->encrypt($value, $cryptoKey));

        self::assertSame($value, (new Encryptor())->decrypt($migratedValue));
    }
}
