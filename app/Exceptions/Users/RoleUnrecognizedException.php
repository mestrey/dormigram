<?php

namespace App\Exceptions\Users;

class RoleUnrecognizedException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Role unrecognized', 404);
    }
}
