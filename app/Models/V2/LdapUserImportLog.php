<?php

namespace App\Models\V2;

use Illuminate\Database\Eloquent\Model;


class LdapUserImportLog extends Model
{
    public $timestamps = false;
    protected $fillable = ['imported_by', 'date', 'inserted_users', 'updated_users', 'type'];
}
