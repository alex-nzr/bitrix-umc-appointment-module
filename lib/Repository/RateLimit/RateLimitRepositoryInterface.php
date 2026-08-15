<?php

namespace ANZ\Appointment\Repository\RateLimit;

interface RateLimitRepositoryInterface
{
    public function increment(string $key, int $expiresAt, int $now): int;
}
