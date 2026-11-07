<?php

namespace ANZ\Appointment\Tests\Unit\Agent;

use ANZ\Appointment\Agent\Exchange;
use Bitrix\Main\Application;
use PHPUnit\Framework\TestCase;

class LogCleanupTest extends TestCase
{
    public function testCleanupPreservesOldProtectionAndDeletesOnlyExpiredLogs(): void
    {
        $previousRoot = Application::$documentRoot;
        $root = dirname(__DIR__, 6) . '/.ai/.tmp/log-cleanup-' . bin2hex(random_bytes(8));
        $logPath = $root . '/local/modules/anz.appointment/logs';
        mkdir($logPath, 0777, true);
        $files = ['.htaccess' => 'Deny from all', '.keepme' => '', 'expired.log' => 'old', 'recent.log' => 'new'];
        try
        {
            foreach ($files as $name => $content)
            {
                file_put_contents($logPath . '/' . $name, $content);
                if ($name !== 'recent.log')
                {
                    touch($logPath . '/' . $name, time() - 40 * 86400);
                }
            }
            Application::$documentRoot = $root;
            Exchange::cleanLogFiles();
            self::assertFileDoesNotExist($logPath . '/expired.log');
            self::assertFileExists($logPath . '/recent.log');
            self::assertFileExists($logPath . '/.keepme');
            self::assertFileExists($logPath . '/.htaccess');
            self::assertSame('Deny from all', file_get_contents($logPath . '/.htaccess'));
        }
        finally
        {
            Application::$documentRoot = $previousRoot;
            foreach (array_keys($files) as $name)
            {
                if (is_file($logPath . '/' . $name))
                {
                    unlink($logPath . '/' . $name);
                }
            }
            foreach ([$logPath, dirname($logPath), dirname($logPath, 2), dirname($logPath, 3), $root] as $directory)
            {
                rmdir($directory);
            }
        }
    }
}
