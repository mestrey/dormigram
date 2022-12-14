<?php

namespace App\Services;

use App\Contracts\Services\AuthAccessServiceContract;
use App\Exceptions\Tokens\ExpiredTokenException;
use App\Exceptions\Tokens\InvalidTokenException;

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

    private function generateHeader(): array
    {
        return [
            'alg' => self::HASHING_ALG,
            'type' => self::TOKEN_TYPE,
        ];
    }

    private function generateRefreshTokenPayload(int $exp): array
    {
        return [
            'exp' => time() + $exp * 60
        ];
    }

    private function generateTokenPayload(int $userId, string $device, int $exp): array
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

    private function decodeData(string $data): array
    {
        return json_decode(base64_decode(str_replace(array('-', '_'), array('+', '/'), $data)), true);
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

    public function createJWTToken(int $userId, string $device): string
    {
        return $this->createToken(
            $this->generateHeader(),
            $this->generateTokenPayload($userId, $device, $this->tokenExp),
            $this->tokenSecret
        );
    }

    public function createResfreshToken(): string
    {
        return $this->createToken(
            $this->generateHeader(),
            $this->generateRefreshTokenPayload($this->refreshTokenExp),
            $this->refreshTokenSecret
        );
    }

    private function explodeToken(string $token)
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            throw new InvalidTokenException();
        }

        return $parts;
    }

    public function getTokenPayload(string $token): array|\Exception
    {
        $parts = $this->explodeToken($token);
        $encodedPayload = $parts[1];

        return $this->decodeData($encodedPayload);
    }

    private function validateToken(string $token, string $secret): bool|\Exception
    {
        $parts = $this->explodeToken($token);
        $encodedHeader = $parts[0];
        $encodedPayload = $parts[1];
        $signature = $parts[2];

        $createdSignature = $this->getTokenSign($encodedHeader, $encodedPayload, $secret);

        if ($signature !== $createdSignature) {
            throw new InvalidTokenException();
        }

        if (time() > $this->decodeData($encodedPayload)['exp']) {
            throw new ExpiredTokenException();
        }

        return true;
    }

    public function validateRefreshToken(string $token): bool|\Exception
    {
        return $this->validateToken($token, $this->refreshTokenSecret);
    }

    public function validateJWTToken(string $token): bool|\Exception
    {
        return $this->validateToken($token, $this->tokenSecret);
    }
}
