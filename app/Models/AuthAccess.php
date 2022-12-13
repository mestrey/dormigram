<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthAccess extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'refresh_token',
    ];

    public function getToken()
    {
        return $this->attributes['token'];
    }

    public function getRefreshToken()
    {
        return $this->attributes['refresh_token'];
    }
}
