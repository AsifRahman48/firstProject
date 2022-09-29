<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Adldap\Laravel\Facades\Adldap;

use Session;
// use App\Http\Controllers\Auth\redirect;
use App\User;
// use Adldap\AdldapInterface;

use Hash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    // use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm(Request $request){
   
          return view('auth.login');
    }


    public function login(Request $request){

    $inputInfo=$request->all();
    $mail=trim($inputInfo['email']);
    $userName=trim($inputInfo['email']);
    $habibUsername='partexstargroup'.'\\'.$mail; 
    $habibPassword=$request['password']; 
    $findUser=$mail.'@psgbd.com';
    $password='123456';


	#### For local test
	$password = Hash::make('123456');
	// exit();
	if (Auth::attempt(['email' => $mail, 'password' => '123456'])) {
		return redirect()->intended('dashboard');
	}
	exit();
	#### For local test


//   $searchLocalUser=User::where('user_name','=',$userName)->count();
//     if($searchLocalUser==0){
               
//      Session::flash('message', "User doesn't match in active directory!"); 
//         Session::flash('alert-class', 'alert-danger');
//       return redirect()->route('login')->withErrors(['msg', 'The Message']);
//     }else{
//       if (Auth::attempt(['user_name' => $userName, 'password' => $password])) {
//                 return redirect()->intended('dashboard');
//             }
//     }

   

// exit();
$authUser = Adldap::auth()->attempt($habibUsername, $habibPassword);

// dd($authUser);
if ($authUser==true) {
 
    $ADauthentic = Adldap::search()->where('samaccountname', '=',$userName)->first();
  //   dd($ADauthentic);
  // exit();
    if(!empty($ADauthentic)){
        $name=$ADauthentic['attributes']['cn'][0];
        $email=$ADauthentic['attributes']['mail'][0]; 
    if(isset($ADauthentic['attributes']['title'][0])){
        $titl=$ADauthentic['attributes']['title'][0];
      }else{
        $titl=NULL;
         }
       $title=$titl;

    if(isset($ADauthentic['attributes']['telephonenumber'][0])){
        $telephonenumber=$ADauthentic['attributes']['telephonenumber'][0];
      }else{
        $telephonenumber=NULL;
      }
        $telephonenumber=$telephonenumber;

    if(isset($value['attributes']['department'][0])){
           $department=$ADauthentic['attributes']['department'][0];
            }else{
            $department=NULL;
          }

    if(isset($value['attributes']['samaccountname'][0])){
          $user_name=$ADauthentic['attributes']['samaccountname'][0];
            }else{
            $user_name=$userName;
          }

        $department=$department; 
        $title=$titl;
        $telephonenumber=$telephonenumber;
        $department=$department;  
       
    $searchLocalUser=User::where('user_name','=',$userName)->count();
    if($searchLocalUser==0){
        $insert=new User();
        $insert->name=$name;
        $insert->email=$email;
        $insert->title=$title;
        $insert->telephonenumber=$telephonenumber;
        $insert->department=$department;
        $insert->user_name=$user_name;
        $insert->save();        
      if (Auth::attempt(['user_name' => $userName, 'password' => $password])) {
                return redirect()->intended('dashboard');
            }
    }else{
      if (Auth::attempt(['user_name' => $userName, 'password' => $password])) {
                return redirect()->intended('dashboard');
            }
    }

    }else{      
        Session::flash('message', "User doesn't match in active directory!"); 
        Session::flash('alert-class', 'alert-danger');
      return redirect()->route('login')->withErrors(['msg', 'The Message']);
   
    }
  }else{
     Session::flash('message', "User doesn't match in active directory! "); 
        Session::flash('alert-class', 'alert-danger');
      return redirect()->route('login')->withErrors(['msg', 'The Message']);  
  }

        }



  public function logout(Request $request){
          Auth::logout();
            $request->session()->invalidate();
        return redirect()->intended('/');
        }
}
