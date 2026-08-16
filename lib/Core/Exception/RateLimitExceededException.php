<?php

namespace ANZ\Appointment\Core\Exception;

use Bitrix\Main\SystemException;

class RateLimitExceededException extends SystemException
{
    public function __construct(
        private readonly string $scope,
        private readonly bool $firstExceeded,
    )
    {
        parent::__construct('Too many requests');
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function isFirstExceeded(): bool
    {
        return $this->firstExceeded;
    }
}
