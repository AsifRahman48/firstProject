<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class TicketHistory extends Model
{
    protected $table = 'ticket_historys';
    // protected $fillable = [
    //     'ticket_id',
    //     'tStatus',
    //     'request_from',
    //     'request_to',
    //     'tDescription'
    // ];

    /**
     * Get the ticket of the specific ticket-history.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }


    /**
     * history log pending
     */
    public static function PENDING(){
    	return 1;
    }


}
