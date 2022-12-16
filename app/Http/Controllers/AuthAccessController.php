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

    private function returnAuthAccess(int $userId, string $device): array
    {
        $authAccess = $this->authAccessRepository->createAuthAccess($userId, $device);

        return $authAccess->toArray();
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

        return $this->returnAuthAccess($user->getId(), $request->userAgent());
    }

    public function refresh(Request $request)
    {
        $data = $this->validate($request, [
            'token' => 'required',
            'refresh_token' => 'required',
        ]);

        $newAuthAccess = $this->authAccessRepository->refreshToken($data['token'], $data['refresh_token']);

        return $newAuthAccess->toArray();
    }

    public function logout()
    {
    }
}
