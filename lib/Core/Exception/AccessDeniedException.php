<?php

namespace ANZ\Appointment\Core\Exception;

use Exception;
use Throwable;

class AccessDeniedException extends Exception
{
    public function __construct(string $message = 'Access denied.', ?Throwable $previous = null)
    {
        parent::__construct($message, 403, $previous);
    }
}
