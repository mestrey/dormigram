<?php

namespace App\Exceptions;

class UnauthorizedException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Unauthorized', 401);
    }
}
