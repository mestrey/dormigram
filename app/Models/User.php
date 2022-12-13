<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function getId()
    {
        return $this->attributes['id'];
    }

    public function getPassword()
    {
        return $this->attributes['password'];
    }
}
