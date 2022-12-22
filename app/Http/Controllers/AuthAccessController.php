<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\AuthAccessRepositoryContract;
use App\Contracts\Repositories\RoleRepositoryContract;
use App\Contracts\Repositories\UserRepositoryContract;
use App\Exceptions\Users\AccountNotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\Users\RoleUnrecognizedException;
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
        $authAccess = $this->authAccessRepository->create($userId, $device);

        return $authAccess->toArray();
    }

    public function register(Request $request, RoleRepositoryContract $roleRepository)
    {
        $data = $this->validate($request, [
            'first_name' => 'required|cyrillic',
            'middle_name' => 'cyrillic',
            'last_name' => 'required|cyrillic',
            'phone' => 'required|phone|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|password',
            'role' => 'required',
        ]);

        $role = $roleRepository->getByName($data['role']) ??
            throw new RoleUnrecognizedException();
        $data['role_id'] = $role->getId();

        $user = $this->userRepository->create($data);

        return $this->returnAuthAccess($user->getId(), $request->userAgent());
    }

    public function login(Request $request)
    {
        $data = $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = $this->userRepository->getByEmail($data['email']) ??
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

    public function logout(Request $request)
    {
        return [
            'success' => (bool)$this->authAccessRepository->removeByToken($request->bearerToken())
        ];
    }
}
