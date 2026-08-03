<?php

namespace ANZ\Appointment\Core\Exception;

use Exception;
use Throwable;

class ExchangeManagerException extends Exception
{
    public function __construct(string $method, Throwable $previous)
    {
        $message = "Exchange error in $method: " . $previous->getMessage();
        parent::__construct(message: $message, previous: $previous);
    }
}
