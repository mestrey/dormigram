<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public const ADMIN_ROLE_ID = 1;
    public const ADMIN_ROLE_NAME = 'admin';

    public const STUDENT_ROLE_ID = 2;
    public const STUDENT_ROLE_NAME = 'student';

    public const COMMANDANT_ROLE_ID = 3;
    public const COMMANDANT_ROLE_NAME = 'commandant';

    public const REPAIRMAN_ROLE_ID = 4;
    public const REPAIRMAN_ROLE_NAME = 'repairman';

    protected $fillable = [
        'name',
    ];

    public function getId()
    {
        return $this->attributes['id'];
    }

    public function getName()
    {
        return $this->attributes['name'];
    }
}
