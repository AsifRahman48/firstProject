<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'user_name', 'title', 'department', 'company_name', 'password_changed_at', 'user_type', 'telephonenumber'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function user_company()
    {
        return $this->hasMany(UserCompany::class, 'user_id', 'id');
    }

    public function user_category(){
        return $this->hasMany(UserCompany::class, 'user_id','id');
    }

    public function getFullNameAttribute()
    {
        $name = $this->name ?? 'empty';
        $title = $this->title ?? 'empty';
        $department = $this->department ?? 'empty';
        $email = $this->email ?? 'empty';
        $company = $this->company_name ?? 'empty';

        return $name . '->' . $title . '->' . $department . '->' . $email . '->' . $company;
    }

}
