<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserRegisterMail extends Mailable
{
    use Queueable, SerializesModels;
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject("Account Creation")->view('emails.registerMail')->with([
            'name' => $this->data['name'],
            'username' => $this->data['username'],
            'password' => $this->data['password'],
        ]);
    }
}
