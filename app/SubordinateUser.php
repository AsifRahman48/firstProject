<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubordinateUser extends Model
{
    protected $fillable = [
        'user_id',
        'subordinate_user',
    ];
}
