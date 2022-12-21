<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone',
        'password',
    ];

    public function getId()
    {
        return $this->attributes['id'];
    }

    public function getPassword()
    {
        return $this->attributes['password'];
    }
}
