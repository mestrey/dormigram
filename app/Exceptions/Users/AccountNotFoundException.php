<?php

namespace App\Exceptions\Users;

class AccountNotFoundException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Account not found', 404);
    }
}
