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
        'role_id',
    ];

    public function getId()
    {
        return $this->attributes['id'];
    }

    public function getRoleId()
    {
        return $this->attributes['role_id'];
    }

    public function getPassword()
    {
        return $this->attributes['password'];
    }

    public function isVerified()
    {
        return $this->attributes['verified'];
    }

    public function verify(bool $verify)
    {
        $this->attributes['verified'] = $verify;
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
