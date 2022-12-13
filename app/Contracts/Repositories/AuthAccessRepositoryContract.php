<?php

namespace App\Contracts\Repositories;

use App\Models\AuthAccess;

interface AuthAccessRepositoryContract
{
    public function createAuthAccess(int $user_id, string $device): AuthAccess;

    public function getAuthAccessesByChunks(int $chunk, callable $callback);
}
