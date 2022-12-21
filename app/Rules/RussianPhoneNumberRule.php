<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;

class RussianPhoneNumberRule
{
    public static function validate()
    {
        Validator::extend('phone', function ($attribute, $value) {
            $value = preg_replace('/\s|\+|-|\(|\)/', '', $value);

            if ($value[0] !== '7' && $value[0] !== '8') {
                return false;
            }

            if (strlen($value) !== 11) {
                return false;
            }

            return true;
        });
    }
}
