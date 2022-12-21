<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;

class PasswordRule
{
    public static function validate()
    {
        Validator::extend('password', function ($attribute, $value) {
            if (!preg_match("#[0-9]+#", $value)) {
                return false;
            }

            if (!preg_match("#[a-z]+#", $value) && !preg_match("#[A-Z]+#", $value)) {
                return false;
            }

            return true;
        });
    }
}
