<?php

namespace App\Exceptions\Tokens;

class NotFoundTokenException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Not Found Token', 404);
    }
}
