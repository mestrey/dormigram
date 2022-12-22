<?php

namespace App\Exceptions\Tokens;

class ExpiredTokenException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Expired Token', 498);
    }
}
