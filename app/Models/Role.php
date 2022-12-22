<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public const ADMIN_ROLE_NAME = 'admin';
    public const STUDENT_ROLE_NAME = 'student';
    public const COMMANDANT_ROLE_NAME = 'commandant';
    public const REPAIRMAN_ROLE_NAME = 'repairman';

    protected $fillable = [
        'name',
    ];

    public function getId()
    {
        return $this->attributes['id'];
    }
}
