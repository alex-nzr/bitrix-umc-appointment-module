<?php

namespace ANZ\Appointment\Core\Exception;

use Exception;
use Throwable;

class ValidatorException extends Exception
{
    public function __construct(string $validationError, ?Throwable $previous = null)
    {
        parent::__construct(
            message: 'Data has validation error: ' . $validationError,
            previous: $previous
        );
    }
}
