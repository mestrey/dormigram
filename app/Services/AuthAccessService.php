<?php

namespace App\Services;

use App\Contracts\Services\AuthAccessServiceContract;

class AuthAccessService implements AuthAccessServiceContract
{
    private const HASHING_ALG = 'sha256';
    private const TOKEN_TYPE = 'jwt';

    public function __construct(
        private string $tokenSecret,
        private string $refreshTokenSecret,
        private int $tokenExp,
        private int $refreshTokenExp,
    ) {
    }

    private function getHeader(): array
    {
        return [
            'alg' => self::HASHING_ALG,
            'type' => self::TOKEN_TYPE,
        ];
    }

    private function getRefreshTokenPayload(string $device, int $exp): array
    {
        return [
            'device' => $device,
            'exp' => time() + $exp * 60
        ];
    }

    private function getTokenPayload(int $userId, string $device, int $exp): array
    {
        return [
            'user_id' => $userId,
            'device' => $device,
            'exp' => time() + $exp * 60,
        ];
    }

    private function encodeData(array $data): string
    {
        return rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');
    }

    private function getTokenSign(string $header, string $payload, string $secret): string
    {
        return hash_hmac(
            self::HASHING_ALG,
            $header . '.' . $payload,
            $secret
        );
    }

    private function createToken(array $header, array $payload, string $secret): string
    {
        $encodedHeader = $this->encodeData($header);
        $encodedPayload = $this->encodeData($payload);

        return implode('.', [$encodedHeader, $encodedPayload, $this->getTokenSign(
            $encodedHeader,
            $encodedPayload,
            $secret
        )]);
    }

    public function createJWTToken(int $user_id, string $device): string
    {
        return $this->createToken(
            $this->getHeader(),
            $this->getTokenPayload($user_id, $device, $this->tokenExp),
            $this->tokenSecret
        );
    }

    public function createResfreshToken(string $device): string
    {
        return $this->createToken(
            $this->getHeader(),
            $this->getRefreshTokenPayload($device, $this->refreshTokenExp),
            $this->refreshTokenSecret
        );
    }
}
