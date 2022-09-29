<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = "audit_logs";
    protected $fillable = [
        'ip',
        'causer_id',
        'activity_name',
        'activity_type',
        'menu_journey',
        'description',
    ];

    public function causer()
    {
        return $this->belongsTo(User::class, 'causer_id', 'id')->select(['id', 'name', 'user_name', 'email', 'telephonenumber']);
    }
}
