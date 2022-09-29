<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Adldap\Laravel\Facades\Adldap;
use Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


public function DoformatSTR(){

$users = Adldap::search()->get();

$dataDictionary=array(); 
foreach ($users as $key => $value) {
 if(isset($value['attributes']['mail'][0]) AND isset($value['attributes']['company'][0])){
   // if(isset($value['attributes']['mail'][0])){
        $name=$value['attributes']['cn'][0];
        $email=$value['attributes']['mail'][0];   
        if(isset($value['attributes']['title'][0])){
           $titl=$value['attributes']['title'][0];
        }else{
             $titl=NULL;
         }
             $title=$titl;

         if(isset($value['attributes']['telephonenumber'][0])){
           $telephonenumber=$value['attributes']['telephonenumber'][0];
            }else{
            $telephonenumber=NULL;
          }
         $telephonenumber=$telephonenumber;
    if(isset($value['attributes']['department'][0])){
           $department=$value['attributes']['department'][0];
            }else{
            $department=NULL;
          }
   
  
        $department=$department;     
               if(isset($value['attributes']['company'][0])){
           $company_name=$value['attributes']['company'][0];
            }else{
            $company_name=NULL;
          }

        $department=$department;     
  $dataDictionary[]=['name'=>$name,'email'=>$email,'title'=>$title,'department'=>$department,'phone'=>$telephonenumber,'company_name'=>$company_name];
 
   // $dataDictionary[]=['name'=>$name,'email'=>$email,'title'=>$title,'department'=>$department,'phone'=>$telephonenumber];
  }
}

return json_encode($dataDictionary);
}

}
