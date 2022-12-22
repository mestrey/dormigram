<?php

namespace App\Contracts\Repositories;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryContract
{
    public function getById(int $id): ?User;
    public function getByEmail(string $email): ?User;

    public function create(array $data): User;

    public function paginateAt(int $perPage, int $pageNumber, bool $verified = false): LengthAwarePaginator;

    public function verifyToggle(int $id): User;
}
