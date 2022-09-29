<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class UserCategory extends Model
{
    public function user_id(){
        return $this->belongsto(User::class, 'user_id','id');
    }

    public function category_id(){
        return $this->belongsto(User::class, 'category_id','id');
    }
}
