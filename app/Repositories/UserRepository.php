<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryContract;
use App\Models\User;

class UserRepository implements UserRepositoryContract
{
    public function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
