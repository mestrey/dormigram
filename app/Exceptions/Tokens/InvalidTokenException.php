<?php

namespace App\Exceptions\Tokens;

class InvalidTokenException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Invalid Token', 498);
    }
}
