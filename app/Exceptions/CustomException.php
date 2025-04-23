<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class CustomException extends Exception
{
    public array $errors;

    public function __construct(string $message, array $errors = [], int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }
}
