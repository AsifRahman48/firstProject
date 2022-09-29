<?php

namespace App\Observers;

use App\Ticket;
use App\Traits\AuditLogTrait;

class TicketObserver
{
    use AuditLogTrait;

    public function created(Ticket $ticket)
    {
//        $this->logStore('created','ticket',"$ticket->tSubject( $ticket->tReference_no ) ticket created.",);
    }

    public function updated(Ticket $ticket)
    {
        //
    }

    public function deleted(Ticket $ticket)
    {
//        $this->logStore('deleted','ticket',"$ticket->tSubject( $ticket->tReference_no ) ticket deleted.");
    }
}
