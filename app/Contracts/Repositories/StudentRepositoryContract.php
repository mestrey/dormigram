<?php

namespace App\Contracts\Repositories;

use App\Models\Student;

interface StudentRepositoryContract
{
    public function create(array $data): Student;
}
