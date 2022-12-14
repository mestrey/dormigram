<?php

namespace App\Repositories;

use App\Contracts\Repositories\AuthAccessRepositoryContract;
use App\Contracts\Services\AuthAccessServiceContract;
use App\Models\AuthAccess;
use Illuminate\Support\Collection;

class AuthAccessRepository implements AuthAccessRepositoryContract
{
    private const MAX_CONNECTIONS_PER_USER = 5;

    public function __construct(
        private AuthAccessServiceContract $authAccessService
    ) {
    }

    private function getAuthAccessesForUserId(int $userId): Collection
    {
        return AuthAccess::where('user_id', $userId)->get();
    }

    private function removeOldestAuthAccesses($authAccesses, $count)
    {
        foreach ($authAccesses->sortBy('created_at')->take($count) as $authAccess) {
            $authAccess->delete();
        }
    }

    public function createAuthAccess(int $userId, string $device): AuthAccess
    {
        $userAuthAccesses = $this->getAuthAccessesForUserId($userId);
        $availableConnections = $userAuthAccesses->count() - self::MAX_CONNECTIONS_PER_USER;

        if ($availableConnections >= 0) {
            $this->removeOldestAuthAccesses($userAuthAccesses, $availableConnections + 1);
        }

        return AuthAccess::create([
            'user_id' => $userId,
            'token' => $this->authAccessService->createJWTToken($userId, $device),
            'refresh_token' => $this->authAccessService->createResfreshToken($device),
        ]);
    }

    public function getAuthAccessesByChunks(int $chunk, callable $callback)
    {
        AuthAccess::chunk($chunk, $callback);
    }
}
