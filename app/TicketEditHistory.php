<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketEditHistory extends Model
{
    protected $fillable = ['ticket_id', 'description', 'edited_by', 'files'];

    public function user()
    {
        return $this->belongsTo(User::class, 'edited_by', 'id');
    }
}
