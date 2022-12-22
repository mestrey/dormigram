<?php

namespace App\Providers;

use App\Contracts\Repositories\AuthAccessRepositoryContract;
use App\Contracts\Services\AuthAccessServiceContract;
use App\Repositories\AuthAccessRepository;
use App\Services\AuthAccessService;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(AuthAccessServiceContract::class, function () {
            return new AuthAccessService(
                env('AUTH_TOKEN_SECRET'),
                env('AUTH_REFRESH_TOKEN_SECRET'),
                env('AUTH_TOKEN_EXP_MIN'),
                env('AUTH_REFRESH_TOKEN_EXP_MIN')
            );
        });

        $this->app->singleton(AuthAccessRepositoryContract::class, AuthAccessRepository::class);
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
    }
}
