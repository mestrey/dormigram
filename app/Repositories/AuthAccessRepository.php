<?php

namespace App\Repositories;

use App\Contracts\Repositories\AuthAccessRepositoryContract;
use App\Contracts\Services\AuthAccessServiceContract;
use App\Exceptions\Tokens\ExpiredTokenException;
use App\Exceptions\Tokens\InvalidTokenException;
use App\Exceptions\Tokens\NotFoundTokenException;
use App\Exceptions\Tokens\ValidTokenException;
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

    public function create(int $userId, string $device, ?string $refreshToken = null): AuthAccess
    {
        $userAuthAccesses = $this->getAuthAccessesByUserId($userId);
        $availableConnections = $userAuthAccesses->count() - self::MAX_CONNECTIONS_PER_USER;

        if ($availableConnections >= 0) {
            $this->removeOldestAuthAccesses($userAuthAccesses, $availableConnections + 1);
        }

        return AuthAccess::create([
            'user_id' => $userId,
            'token' => $this->authAccessService->createJWTToken($userId, $device),
            'refresh_token' => $refreshToken ?? $this->authAccessService->createResfreshToken(),
        ]);
    }

    public function refreshAuthAccess(int $userId, string $device, string $refreshToken): AuthAccess
    {
        $payload = $this->authAccessService->getTokenPayload($refreshToken);
        $payload['used']++;

        $newRefreshToken = $this->authAccessService->updateResfreshToken($payload['exp'], $payload['used']);

        return $this->create($userId, $device, $newRefreshToken);
    }

    public function getByChunks(int $chunk, callable $callback)
    {
        AuthAccess::chunk($chunk, $callback);
    }

    public function removeByToken(string $token): bool
    {
        $authAccess = AuthAccess::where('token', $token)->first();

        if (empty($authAccess)) {
            throw new InvalidTokenException();
        }

        return $authAccess->delete();
    }

    private function getAuthAccessByTokens(string $token, string $refreshToken): AuthAccess|\Exception
    {
        $authAccessByToken = AuthAccess::where('token', $token)->first();
        $authAccessByRefreshToken = AuthAccess::where('refresh_token', $refreshToken)->first();

        $ifOnlyOneExist = function (?AuthAccess $a, ?AuthAccess $b) {
            if (empty($a) && !empty($b)) {
                $b->delete();
                throw new InvalidTokenException();
            }
        };

        $ifOnlyOneExist($authAccessByToken, $authAccessByRefreshToken);
        $ifOnlyOneExist($authAccessByRefreshToken, $authAccessByToken);

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
        $tokenExpired = false;

        try {
            $this->authAccessService->validateJWTToken($token);
        } catch (\Exception $e) {
            if (!$e instanceof ExpiredTokenException) {
                throw $e;
            } else {
                $tokenExpired = true;
            }
        }

        if (!$tokenExpired) {
            throw new ValidTokenException();
        }

        $this->authAccessService->validateRefreshToken($refreshToken);
        $tokenPayload = $this->authAccessService->getTokenPayload($token);

        return $this->refreshAuthAccess($tokenPayload['user_id'], $tokenPayload['device'], $refreshToken);
    }
}
