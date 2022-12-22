<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'room',
    ];

    public function user()
    {
        $this->belongsTo(User::class);
    }
}
