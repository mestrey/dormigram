<?php

namespace App\Contracts\Services;

interface AuthAccessServiceContract
{
    public function __construct(
        string $tokenSecret,
        string $refreshTokenSecret,
        int $tokenExp,
        int $refreshTokenExp,
    );

    public function createJWTToken(int $user_id, string $device): string;
    public function createResfreshToken(string $device): string;

    public function isValidRefreshToken(string $token): bool|\Exception;
}
