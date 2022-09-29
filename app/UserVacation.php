<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserVacation extends Model
{
    protected $fillable = ['leave_type_id', 'user_id', 'from_date', 'to_date', 'forward_user_id', 'reason', 'status'];

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'forward_user_id', 'id');
    }
}
