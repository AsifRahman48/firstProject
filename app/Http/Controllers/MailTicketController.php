<?php

namespace App\Http\Controllers;

use App\User;
use App\Mail\TestMail;
use App\Ticket;
use App\TicketApprove;
use Illuminate\Http\Request;
use App\Mail\UserRegisterMail;
use Illuminate\Support\Facades\Mail;

class MailTicketController extends Controller
{
    public function mail(){
        $data=[
            'title'=>'Mail from Surfside Media',
            'body'=>'This is testing mail',
        ];
        Mail::to('3e6e6e9c66-41b579@inbox.mailtrap.io')->send(new TestMail($data));
        Return "Mail Send";
    }

    public function sendmail()
    {
        $notification=Ticket::where('tStatus', '!=', 1)->where('tStatus', '!=', 4)->where('tStatus', '!=', 5)->get();
        //dd($notification);
        foreach ($notification as $list)
        {
            $a=$list->thistory;
            $explode_id = json_decode($a, true);
            //return $explode_id;
        }
          //  dd($explode_id);
//       array([
//                'user_type'
//        ]);
       // $user_type=TicketApprove::get('user_type');

        foreach ($explode_id as $list){
               if($list['user_status']="pending"){
                   $this->mail();
                   break;
               }
               // dd($n);
                 // return redirect('/');

        }

        return "Mail Send";
//        $n=$explode_id->user_type;

//        $n=Ticket::get('thistory');
//        $arr=str_split($n);
//        $collections=collect($arr);
//       // echo str_word_count($n,0);
//       // $explode_id = json_decode($n->Ticket, true);
//        dd($collections);
        //print_r($arr);
        //$notification=$this->mail();
       //dd($notification);
       // return $notification;
    }
//        //echo 'hi';

}
