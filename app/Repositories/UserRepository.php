<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryContract;
use App\Models\User;
use App\Rules\RussianPhoneNumberRule;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryContract
{
    public function getById(int $id): ?User
    {
        return User::where('id', $id)->first();
    }

    public function getByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function create(array $data): User
    {
        $data['phone'] = preg_replace(RussianPhoneNumberRule::PHONE_CLEAN_REGEX, '', $data['phone']);
        $data['phone'][0] = '8';
        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }

    public function paginateAt(int $perPage, int $pageNumber, bool $verified = false): LengthAwarePaginator
    {
        return User::where('verified', $verified)->with(['students'])->orderBy('created_at')->paginate($perPage, ['*'], 'page', $pageNumber);
    }

    public function verifyToggle(int $id): User
    {
        $user = $this->getById($id);
        $user->verify(!$user->isVerified());
        $user->save();

        return $user;
    }
}
