<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\AuthAccessRepositoryContract;
use App\Contracts\Repositories\UserRepositoryContract;
use App\Exceptions\AccountNotFoundException;
use App\Exceptions\UnauthorizedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Routing\Controller;

class AuthAccessController extends Controller
{
    public function __construct(
        private UserRepositoryContract $userRepository,
        private AuthAccessRepositoryContract $authAccessRepository,
    ) {
    }

    private function returnAuthAccess(string $token, string $refreshToken): array
    {
        return ['data' => [
            'token' => $token,
            'refresh_token' => $refreshToken,
        ]];
    }

    public function register(Request $request)
    {
    }

    public function login(Request $request)
    {
        $data = $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = $this->userRepository->getUserByEmail($data['email']) ??
            throw new AccountNotFoundException();

        if (!Hash::check($data['password'], $user->getPassword())) {
            throw new UnauthorizedException();
        }

        $authAccess = $this->authAccessRepository->createAuthAccess($user->getId(), $request->userAgent());

        return $this->returnAuthAccess($authAccess->getToken(), $authAccess->getRefreshToken());
    }

    public function refresh(Request $request)
    {
        $data = $this->validate($request, [
            'token' => 'required',
            'refresh_token' => 'required',
        ]);

        try {
            $newToken = $this->authAccessRepository->refreshToken($data['token'], $data['refresh_token']);

            return $this->returnAuthAccess($newToken->getToken(), $newToken->getRefreshToken());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function logout()
    {
    }
}
