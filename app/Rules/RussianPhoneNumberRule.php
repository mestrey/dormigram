<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;

class RussianPhoneNumberRule
{
    public const PHONE_CLEAN_REGEX = '/\s|\+|-|\(|\)/';

    public static function validate()
    {
        Validator::extend('phone', function ($attribute, $value) {
            $value = preg_replace(self::PHONE_CLEAN_REGEX, '', $value);

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
