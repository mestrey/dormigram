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

        return ['data' => [
            'token' => $authAccess->getToken(),
            'refresh_token' => $authAccess->getRefreshToken(),
        ]];
    }

    public function refresh(Request $request)
    {
    }

    public function logout()
    {
    }
}
