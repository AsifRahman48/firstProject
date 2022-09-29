<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class UserCompany extends Model
{
    protected $table = 'users_company';
    protected $fillable = [
        'id',
        'user_id',
        'company_id'
    ];

    /**
     * Get the category of the specific tickets.
     */
    public function user_id()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the category of the specific tickets.
     */
    public function company_id()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
