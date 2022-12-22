<?php

namespace App\Contracts\Repositories;

use App\Models\AuthAccess;

interface AuthAccessRepositoryContract
{
    public function create(int $userId, string $device, ?string $refreshToken = null): AuthAccess;

    public function getByChunks(int $chunk, callable $callback);

    public function refreshToken(string $token, string $refreshToken): AuthAccess|\Exception;

    public function removeByToken(string $token): bool;
}
