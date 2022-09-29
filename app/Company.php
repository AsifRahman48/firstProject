<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Company extends Model
{
    protected $table = 'company_name';

    public function user_company()
    {
        return $this->hasMany(UserCompany::class, 'company_id', 'id');
    }
}
