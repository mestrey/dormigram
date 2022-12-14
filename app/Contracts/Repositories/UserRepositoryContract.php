<?php

namespace App\Contracts\Repositories;

use App\Models\User;

interface UserRepositoryContract
{
    public function getUserById(int $id): ?User;
    public function getUserByEmail(string $email): ?User;
}
