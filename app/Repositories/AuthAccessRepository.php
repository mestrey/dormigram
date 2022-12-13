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

    private function getAuthAccessesForUserId(int $user_id): Collection
    {
        return AuthAccess::where('user_id', $user_id)->get();
    }

    private function removeOldestAuthAccesses($authAccesses, $count)
    {
        foreach ($authAccesses->sortBy('created_at')->take($count) as $authAccess) {
            $authAccess->delete();
        }
    }

    public function createAuthAccess(int $user_id, string $device): AuthAccess
    {
        $userAuthAccesses = $this->getAuthAccessesForUserId($user_id);
        $availableConnections = $userAuthAccesses->count() - self::MAX_CONNECTIONS_PER_USER;

        if ($availableConnections >= 0) {
            $this->removeOldestAuthAccesses($userAuthAccesses, $availableConnections + 1);
        }

        return AuthAccess::create([
            'user_id' => $user_id,
            'token' => $this->authAccessService->createJWTToken($user_id, $device),
            'refresh_token' => $this->authAccessService->createResfreshToken($device),
        ]);
    }
}
