<?php

namespace App\Repositories;

use App\Contracts\Repositories\RoleRepositoryContract;
use App\Models\Role;

class RoleRepository implements RoleRepositoryContract
{
    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function truncate()
    {
        return Role::truncate();
    }
}
