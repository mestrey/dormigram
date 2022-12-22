<?php

namespace App\Repositories;

use App\Contracts\Repositories\StudentRepositoryContract;
use App\Models\Student;

class StudentRepository implements StudentRepositoryContract
{
    public function create(array $data): Student
    {
        return Student::create($data);
    }
}
