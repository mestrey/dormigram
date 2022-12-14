<?php

namespace App\Repositories;

use App\Contracts\Repositories\AuthAccessRepositoryContract;
use App\Contracts\Services\AuthAccessServiceContract;
use App\Exceptions\Tokens\ExpiredTokenException;
use App\Exceptions\Tokens\InvalidTokenException;
use App\Exceptions\Tokens\NotFoundTokenException;
use App\Models\AuthAccess;
use Illuminate\Support\Collection;

class AuthAccessRepository implements AuthAccessRepositoryContract
{
    private const MAX_CONNECTIONS_PER_USER = 5;

    public function __construct(
        private AuthAccessServiceContract $authAccessService
    ) {
    }

    private function getAuthAccessesByUserId(int $userId): Collection
    {
        return AuthAccess::where('user_id', $userId)->get();
    }

    private function removeOldestAuthAccesses($authAccesses, $count)
    {
        foreach ($authAccesses->sortBy('created_at')->take($count) as $authAccess) {
            $authAccess->delete();
        }
    }

    public function createAuthAccess(int $userId, string $device, ?string $refreshToken = null): AuthAccess
    {
        $userAuthAccesses = $this->getAuthAccessesByUserId($userId);
        $availableConnections = $userAuthAccesses->count() - self::MAX_CONNECTIONS_PER_USER;

        if ($availableConnections >= 0) {
            $this->removeOldestAuthAccesses($userAuthAccesses, $availableConnections + 1);
        }

        return AuthAccess::create([
            'user_id' => $userId,
            'token' => $this->authAccessService->createJWTToken($userId, $device),
            'refresh_token' => $refreshToken ?? $this->authAccessService->createResfreshToken($device),
        ]);
    }

    public function getAuthAccessesByChunks(int $chunk, callable $callback)
    {
        AuthAccess::chunk($chunk, $callback);
    }

    private function getAuthAccessByTokens(string $token, string $refreshToken): AuthAccess|\Exception
    {
        $authAccessByToken = AuthAccess::where('token', $token)->first();
        $authAccessByRefreshToken = AuthAccess::where('refresh_token', $refreshToken)->first();

        $ifOneExist = function (?AuthAccess $a, ?AuthAccess $b) {
            if (empty($a) && !empty($b)) {
                $b->delete();
                throw new InvalidTokenException();
            }
        };

        $ifOneExist($authAccessByToken, $authAccessByRefreshToken);
        $ifOneExist($authAccessByRefreshToken, $authAccessByToken);

        if (empty($authAccessByToken) && empty($authAccessByRefreshToken)) {
            throw new NotFoundTokenException();
        }

        if (!$authAccessByToken->is($authAccessByRefreshToken)) {
            $authAccessByToken->delete();
            $authAccessByRefreshToken->delete();
            throw new InvalidTokenException();
        }

        return $authAccessByToken;
    }

    public function refreshToken(string $token, string $refreshToken): AuthAccess|\Exception
    {
        $authAccess = $this->getAuthAccessByTokens($token, $refreshToken);
        $authAccess->delete();
        $dataToken = [];

        try {
            $dataToken = $this->authAccessService->validateJWTToken($token);
        } catch (\Exception $e) {
            if (!$e instanceof ExpiredTokenException) {
                throw $e;
            }
        }

        try {
            $this->authAccessService->validateRefreshToken($refreshToken);
        } catch (\Exception $e) {
            throw $e;
        }

        return $this->createAuthAccess($dataToken['user_id'], $dataToken['device'], $refreshToken);
    }
}
