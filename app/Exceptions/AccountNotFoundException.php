<?php

namespace App\Exceptions;

class AccountNotFoundException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Account not found', 404);
    }
}
