<?php

namespace App\Contracts\Repositories;

use App\Models\User;

interface UserRepositoryContract
{
    public function getById(int $id): ?User;
    public function getByEmail(string $email): ?User;

    public function create(array $data): User;
}
