<?php

namespace App\Exceptions\Tokens;

class ValidTokenException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Still Valid Token', 498);
    }
}
