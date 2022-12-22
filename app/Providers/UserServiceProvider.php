<?php

namespace App\Providers;

use App\Contracts\Repositories\RoleRepositoryContract;
use App\Contracts\Repositories\StudentRepositoryContract;
use App\Contracts\Repositories\UserRepositoryContract;
use App\Repositories\RoleRepository;
use App\Repositories\StudentRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(UserRepositoryContract::class, UserRepository::class);
        $this->app->singleton(RoleRepositoryContract::class, RoleRepository::class);
        $this->app->singleton(StudentRepositoryContract::class, StudentRepository::class);
    }
}
