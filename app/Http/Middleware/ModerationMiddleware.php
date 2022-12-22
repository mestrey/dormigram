<?php

namespace App\Http\Middleware;

use App\Exceptions\UnauthorizedException;
use App\Models\Role;
use Closure;

class ModerationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $roleId = $request->user()->getRoleId();

        if (
            !$roleId === Role::ADMIN_ROLE_ID &&
            !$roleId === Role::COMMANDANT_ROLE_ID
        ) {
            throw new UnauthorizedException();
        }

        return $next($request);
    }
}
