<?php

namespace ANZ\Appointment\Service\Security;

use Bitrix\Main\ArgumentException;

class OneCUrlGuard
{
    private const HTTP_PORT = 80;
    private const HTTPS_PORT = 443;
    private const MIN_PORT = 1;
    private const MAX_PORT = 65535;
    private const LOCALHOST_HOSTS = ['localhost', 'localhost.localdomain'];
    private const FORBIDDEN_IPV4_RANGES = [
        ['0.0.0.0', '0.255.255.255'],
        ['127.0.0.0', '127.255.255.255'],
        ['169.254.0.0', '169.254.255.255'],
        ['224.0.0.0', '255.255.255.255'],
    ];

    public function assertAllowed(string $url): void
    {
        $parts = parse_url($url);
        if (!is_array($parts))
        {
            throw new ArgumentException('Invalid URL');
        }

        $scheme = mb_strtolower((string)($parts['scheme'] ?? ''));
        if (!in_array($scheme, ['http', 'https'], true))
        {
            throw new ArgumentException('Invalid URL scheme');
        }

        $host = trim((string)($parts['host'] ?? ''));
        if ($host === '')
        {
            throw new ArgumentException('URL host is empty');
        }

        $port = (int)($parts['port'] ?? ($scheme === 'https' ? self::HTTPS_PORT : self::HTTP_PORT));
        if ($port < self::MIN_PORT || $port > self::MAX_PORT)
        {
            throw new ArgumentException('Invalid URL port');
        }

        $normalizedHost = mb_strtolower($host);
        if (in_array($normalizedHost, self::LOCALHOST_HOSTS, true))
        {
            throw new ArgumentException('Localhost is not allowed');
        }

        foreach ($this->resolveHost($host) as $ip)
        {
            if ($this->isForbiddenIp($ip))
            {
                throw new ArgumentException('URL host is not allowed');
            }
        }
    }

    private function resolveHost(string $host): array
    {
        if (filter_var($host, FILTER_VALIDATE_IP))
        {
            return [$host];
        }

        $records = dns_get_record($host, DNS_A + DNS_AAAA) ?: [];
        $ips = [];
        foreach ($records as $record)
        {
            foreach (['ip', 'ipv6'] as $field)
            {
                if (!empty($record[$field]))
                {
                    $ips[] = (string)$record[$field];
                }
            }
        }

        return $ips;
    }

    private function isForbiddenIp(string $ip): bool
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
        {
            $long = ip2long($ip);
            foreach (self::FORBIDDEN_IPV4_RANGES as [$from, $to])
            {
                if ($long >= ip2long($from) && $long <= ip2long($to))
                {
                    return true;
                }
            }
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
        {
            $normalized = mb_strtolower($ip);
            return $normalized === '::1'
                || str_starts_with($normalized, 'fe80:')
                || str_starts_with($normalized, 'ff');
        }

        return false;
    }
}
