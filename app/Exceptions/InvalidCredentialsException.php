<?php

namespace App\Exceptions;

use Illuminate\Validation\UnauthorizedException;

class InvalidCredentialsException extends UnauthorizedException
{
    public function __construct($message = 'Invalid Credentials!', \Exception $previous = null, $code = 0)
    {
        parent::__construct($message, $code, $previous);
    }
}
