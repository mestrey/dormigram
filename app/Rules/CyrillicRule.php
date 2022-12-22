<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;

class CyrillicRule
{
    public static function validate()
    {
        Validator::extend('cyrillic', function ($attribute, $value) {
            return preg_match('/[А-Яа-яЁё]/u', $value);
        });
    }
}
