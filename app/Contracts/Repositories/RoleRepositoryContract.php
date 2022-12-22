<?php

namespace App\Contracts\Repositories;

use App\Models\Role;

interface RoleRepositoryContract
{
    public function create(array $data): Role;

    public function truncate();
}
