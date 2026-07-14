<?php

namespace ANZ\Appointment\Service\Security;

use Bitrix\Main\Application;
use Bitrix\Main\SystemException;

class RateLimiter
{
    public function assertAllowed(string $scope, int $limit, int $ttl, string $identity = ''): void
    {
        $session = Application::getInstance()->getSession();
        $key = 'anz_appointment_rate_' . md5($scope . '|' . $identity . '|' . $this->getIp());
        $data = $session->get($key);
        $now = time();

        if (!is_array($data) || ($data['expires'] ?? 0) < $now)
        {
            $data = [
                'count' => 0,
                'expires' => $now + $ttl,
            ];
        }

        $data['count']++;
        $session->set($key, $data);

        if ($data['count'] > $limit)
        {
            throw new SystemException('Too many requests');
        }
    }

    private function getIp(): string
    {
        $server = Application::getInstance()->getContext()->getServer();
        return (string)($server->get('REMOTE_ADDR') ?: 'unknown');
    }
}
