<?php

namespace App\Contracts\Repositories;

use App\Models\User;

interface UserRepositoryContract
{
    public function getUserByEmail(string $email): ?User;
}
