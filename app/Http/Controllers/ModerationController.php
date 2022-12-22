<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\UserRepositoryContract;
use App\Exceptions\Users\AccountNotFoundException;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class ModerationController extends Controller
{
    private const MAX_PAGINATE = 100;

    public function __construct(
        private UserRepositoryContract $userRepository
    ) {
    }

    public function users(Request $request)
    {
        $pageNumber = intval($request->query()['page'] ?? 1);
        $perPage = intval($request->query()['per'] ?? self::MAX_PAGINATE);
        $verified = boolval($request->query()['verified'] ?? false);

        return $this->userRepository->paginateAt($perPage, $pageNumber, $verified)->toJson();
    }

    public function verify(int $userId)
    {
        try {
            return ['success' => $this->userRepository->verifyToggle($userId)];
        } catch (\Throwable $_) {
            throw new AccountNotFoundException();
        }
    }
}
