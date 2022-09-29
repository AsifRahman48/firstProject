<?php

namespace App\Http\Helpers;

use Auth;


class Helper
{
    public static function checkAdmin($pageName,$data){
      $user = Auth::user()->user_type;
      if($user != 1){
        return view('unauthorized');
      }else{
        return view($pageName, compact('data'));
      }
    }

    public static function getStatusList()
    {
        $statusList[1]  = 'Save as Draft ';
        $statusList[2]  = 'New Request ';
        $statusList[3]  = 'Draft by Approver ';
        $statusList[4]  = 'Request Approved';
        $statusList[5]  = 'Request Rejected';
        $statusList[6]  = 'Request for Info';
        $statusList[7]  = 'Forward';
        $statusList[8]  = 'Appoved And Forward';
        $statusList[9]  = 'Appoved And Acknowledgement';
        $statusList[10] = 'Disable';
        $statusList[11] = 'Pending';
        return $statusList;
    }
}
