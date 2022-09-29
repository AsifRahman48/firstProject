<?php

namespace App\Http\Controllers\Auth;

use Adldap\Laravel\Facades\Adldap;
use App\Http\Controllers\Controller;
use App\User;
use Hash;
use Illuminate\Http\Request;
// use App\Http\Controllers\Auth\redirect;
use Illuminate\Support\Facades\Auth;
// use Adldap\AdldapInterface;

use Session;

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

    public function showLoginForm(Request $request)
    {

        return view('auth.login');
    }

    private function authWithDatabase($input){
        $mail          =  trim($input['email']);
        $userName      =  trim($input['email']);
        $password      =  trim($input['password']);

        //$searchLocalUser=User::where('user_name','=',$userName)->count();
//        if($searchLocalUser==0){
//
//            Session::flash('message', "User doesn't match in database directory!");
//            Session::flash('alert-class', 'alert-danger');
//            return false;
//        }else{


            if (Auth::attempt(['user_name' => $userName, 'password' => $password]) || Auth::attempt(['email' => $mail, 'password' => $password])) {
                return true;
            }

            Session::flash('message', "These credentials do not match our records.");
            Session::flash('alert-class', 'alert-danger');
            return false;
        //}


    }

    private function authWithLdap($input){
        $userName      =  trim($input['email']);
        $password      =  trim($input['password']);
        $password      =   '123456';
        //LDAP
        $habibUsername = 'partexstargroup' . '\\' . trim($input['email']);
        $habibPassword = $input['password'];

        #LDAP
        $authUser = Adldap::auth()->attempt($habibUsername, $habibPassword);


        if ($authUser == true) {

            $ADauthentic = Adldap::search()->where('samaccountname', '=', $userName)->first();


            // exit();
            if (!empty($ADauthentic)) {
                $name  = $ADauthentic['attributes']['cn'][0];
                $email = $ADauthentic['attributes']['mail'][0];
                if (isset($ADauthentic['attributes']['title'][0])) {
                    $titl = $ADauthentic['attributes']['title'][0];
                } else {
                    $titl = null;
                }
                $title = $titl;

                if (isset($ADauthentic['attributes']['telephonenumber'][0])) {
                    $telephonenumber = $ADauthentic['attributes']['telephonenumber'][0];
                } else {
                    $telephonenumber = null;
                }
                $telephonenumber = $telephonenumber;


                if (isset($value['attributes']['department'][0])) {
                    $department = $ADauthentic['attributes']['department'][0];
                } else {
                    $department = null;
                }




                # company name
                if (isset($value['attributes']['company'][0])) {
                    $company_name = $ADauthentic['attributes']['company'][0];
                } else {
                    $company_name = null;
                }


                if (isset($value['attributes']['samaccountname'][0])) {
                    $user_name = $ADauthentic['attributes']['samaccountname'][0];
                } else {
                    $user_name = $userName;
                }


                // $department      = $department;
                $title           = $titl;
                $telephonenumber = $telephonenumber;
                $department      = $department;
                $company_name      = $company_name;


                $searchLocalUser = User::where('user_name', '=', $userName)->first();




                if ( empty($searchLocalUser) ) {
                    $insert                  = new User();
                    $insert->name            = $name;
                    $insert->email           = $email;
                    $insert->title           = $title;
                    $insert->telephonenumber = $telephonenumber;
                    $insert->department      = $department;
                    $insert->user_name       = $user_name;
                    $insert->company_name       = $company_name;
                    $insert->save();
                    if (Auth::attempt(['user_name' => $userName, 'password' => $password])) {
                        return true;
                    }
                } else {
                    if (Auth::attempt(['user_name' => $userName, 'password' => $password])) {

                        # User Exists but need to check department, designation, company update
                        if( $searchLocalUser->title == null || $searchLocalUser->department == null || $searchLocalUser->company_name == null ){

                            $userUpdateArray = [
                                'name' => $name,
                                // 'user_name' => trim($userNames),
                                // 'email' => trim($email),
                                'title' => $title,
                                'department' => $department,
                                'telephonenumber' => $telephonenumber,
                                'company_name' => $company_name
                            ];

                            User::where('user_name', '=', $userName)->update($userUpdateArray);
                            return true;

                        }
                        else{
                            return true;
                        }

                    }
                }

            } else {
                Session::flash('message', "User doesn't match in active directory!");
                Session::flash('alert-class', 'alert-danger');
                return false;

            }
        }
        else {
            Session::flash('message', "User doesn't match in active directory! ");
            Session::flash('alert-class', 'alert-danger');
            return false;
        }
    }

    public function login(Request $request)
    {


        $inputInfo     = $request->all();



        switch (config('custom.settings.authentication')){
            case 'database' :
                if($this->authWithDatabase($inputInfo)){
                    return redirect()->intended('dashboard');
                }
                else{
                    return redirect()->route('login')->withErrors(['msg', 'The Message']);
                }
                break;
            case 'ldap':
                if($this->authWithLdap($inputInfo)){
                    return redirect()->intended('dashboard');
                }
                else{
                    return redirect()->route('login')->withErrors(['msg', 'The Message']);
                }
                break;
            default:
                $this->authWithDatabase($inputInfo);

        }



    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        return redirect()->intended('/');
    }
}

