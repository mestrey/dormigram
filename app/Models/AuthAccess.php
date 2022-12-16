<?php

namespace App\Models;

use App\Contracts\Services\AuthAccessServiceContract;
use Illuminate\Database\Eloquent\Model;

class AuthAccess extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'refresh_token',
    ];

    protected $visible = [
        'token',
        'refresh_token'
    ];

    public function getId()
    {
        return $this->attributes['id'];
    }

    public function getToken()
    {
        return $this->attributes['token'];
    }

    public function getRefreshToken()
    {
        return $this->attributes['refresh_token'];
    }
}
