<?php

namespace App\Http\Middleware;

use App\Contracts\Repositories\UserRepositoryContract;
use App\Contracts\Services\AuthAccessServiceContract;
use App\Exceptions\Users\AccountNotFoundException;
use App\Exceptions\Tokens\InvalidTokenException;
use App\Exceptions\UnauthorizedException;
use Closure;

class AuthMiddleware
{
    public function __construct(
        private AuthAccessServiceContract $authAccessService,
        private UserRepositoryContract $userRepository,
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken() ??
            throw new UnauthorizedException();

        $this->authAccessService->validateJWTToken($token);
        $tokenPayload = $this->authAccessService->getTokenPayload($token);

        if ($tokenPayload['device'] !== $request->userAgent()) {
            throw new InvalidTokenException();
        }

        $user = $this->userRepository->getById($tokenPayload['user_id']) ??
            throw new AccountNotFoundException();

        $request->merge(['user' => $user]);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}
