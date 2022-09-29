<?php

namespace App\Observers;

use App\Traits\AuditLogTrait;
use App\User;

class UserObserver
{
    use AuditLogTrait;

    public function created(User $user)
    {
//        $this->logStore('created','user',"$user->name( $user->email ) user created.");
    }

    /*public function updated(User $user)
    {
        $this->logStore('updated','user',"$user->name( $user->email ) user updated.");
    }*/

    public function deleted(User $user)
    {
//        $this->logStore('deleted','user',"$user->name( $user->email ) user deleted.");
    }
}
