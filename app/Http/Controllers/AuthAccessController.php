<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\AuthAccessRepositoryContract;
use App\Contracts\Repositories\RoleRepositoryContract;
use App\Contracts\Repositories\StudentRepositoryContract;
use App\Contracts\Repositories\UserRepositoryContract;
use App\Exceptions\Users\AccountNotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\Users\RoleUnrecognizedException;
use App\Models\Role;
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

    public function register(
        Request $request,
        RoleRepositoryContract $roleRepository,
        StudentRepositoryContract $studentRepository,
    ) {
        $userData = $this->validate($request, [
            'first_name' => 'required|cyrillic|max:30',
            'middle_name' => 'cyrillic|max:30',
            'last_name' => 'required|cyrillic|max:30',
            'phone' => 'required|phone|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|password',
            'role' => 'required',
        ]);

        $role = $roleRepository->getByName($userData['role']) ??
            throw new RoleUnrecognizedException();
        $userData['role_id'] = $role->getId();

        $createUser = function () use ($userData) {
            return $this->userRepository->create($userData);
        };

        switch ($role->getName()) {
            case Role::STUDENT_ROLE_NAME:
                $studentData = $this->validate($request, [
                    'room' => 'required',
                ]);
                $user = $createUser();
                $studentData['user_id'] = $user->getId();
                $studentRepository->create($studentData);
                break;
            default:
                $user = $createUser();
                break;
        }

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
