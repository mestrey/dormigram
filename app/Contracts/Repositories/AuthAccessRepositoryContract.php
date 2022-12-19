<?php

namespace App\Contracts\Repositories;

use App\Models\AuthAccess;

interface AuthAccessRepositoryContract
{
    public function createAuthAccess(int $userId, string $device, ?string $refreshToken = null): AuthAccess;

    public function getAuthAccessesByChunks(int $chunk, callable $callback);

    public function refreshToken(string $token, string $refreshToken): AuthAccess|\Exception;

    public function removeAuthAccessByToken(string $token): bool;
}
