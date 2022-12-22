<?php

namespace App\Providers;

use App\Rules\CyrillicRule;
use App\Rules\PasswordRule;
use App\Rules\RussianPhoneNumberRule;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        CyrillicRule::validate();
        RussianPhoneNumberRule::validate();
        PasswordRule::validate();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
