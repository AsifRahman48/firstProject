<?php

namespace App\Http\Controllers;

use Adldap\Laravel\Facades\Adldap;
use App\Company;
use App\Contracts\ILdapService;
use App\Http\Requests\StoreUser;
use App\Mail\UserRegisterMail;
use App\Traits\AuditLogTrait;
use App\User;
use App\UserCompany;
use App\UserCategory;
use App\Category;
use Carbon\Carbon;
use DB;
use Exception;
use App\Http\Helpers\Helper;
use Illuminate\Http\Request;
// use Adldap\Adldap;
// use Adldap\AdldapInterface;
// use Adldap\Adldap;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

// use Adldap\Laravel\Facades\Adldap as ADLDAPFACADES;

class UserController extends Controller
{
    use AuditLogTrait;

    protected $ldapService;

    public function __construct(ILdapService $ldapService)
    {
        $this->middleware('auth');
        // $this->middleware('role:Admin');
        $this->ldapService = $ldapService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(request $request)
    {
        $data = [
            'pageTitle'   => 'User List',
            'ctrlName'    => 'user',
            'CompanyName' => Company::where('active_date', '<=', date('Y-m-d'))
                ->Where(function ($query) {
                    $query->whereNull('deactive_date');
                    $query->orWhere('deactive_date', '>=', date('Y-m-d'));
                })
                ->orderBy('name')
                ->pluck('name', 'id'),
            'CategoryName' => Category::all()->pluck('name','id'),
        ];

        $searchTerm = $request['searchTerm'];
        $role = $request->role;
        if (isset($searchTerm) || isset($role)) {
            $searchTerm       = strtolower($searchTerm);
            $data['listData'] = User::whereNotNull('user_type')
                ->when($searchTerm, function ($query) use ($searchTerm) {
                    $query->where(function ($query) use ($searchTerm) {
                        $query->whereRaw("LOWER(name) LIKE '%{$searchTerm}%'");
                        $query->orWhereRaw("LOWER(email) LIKE '%{$searchTerm}%'");
                        $query->orWhereRaw("LOWER(title) LIKE '%{$searchTerm}%'");
                        $query->orWhereRaw("LOWER(company_name) LIKE '%{$searchTerm}%'");
                        $query->orWhereRaw("LOWER(department) LIKE '%{$searchTerm}%'");
                        $query->orWhereRaw("LOWER(telephonenumber) LIKE '%{$searchTerm}%'");
                    });
                })
                ->when($role, function ($query) use ($role) {
                    $query->where('user_type', '=', $role == 4 ? 0 : $role);
                })
                ->orderBy('name')->orderBy('id', 'DESC')->paginate(10);

            $data['searchTerm'] = $request['searchTerm'];

        }
        else {
            $data['listData'] = User::orderBy('name')->orderBy('id', 'DESC')->paginate(10);
        }
        $pageName='user.index';
        return Helper::checkAdmin($pageName,$data);
    }

    public function getUserDeparmentList(Request $request)
    {
        return DB::table('user_categories')
            ->select('categorys.id as catId', 'categorys.name as name')
            ->join('categorys','user_categories.category_id','=','categorys.id')
            ->where('user_categories.user_id', '=' , $request->userId)
            ->pluck('catId')
            ->toArray();
    }

    public function getUserCompanyList(Request $request)
    {
       return DB::table('users_company')
            ->select('company_name.id as comId', 'company_name.name as name')
            ->join('company_name','users_company.company_id','=','company_name.id')
            ->where('users_company.user_id', '=' , $request->userId)
            ->pluck('comId')
            ->toArray();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(config('custom.settings.authentication') != 'database') {
            abort(403);
        }

        $data = [
            'pageTitle' => 'User Add',
            // 'userType'  => Role::orderBy('id', 'DESC')->pluck('name', 'id'),
            'ctrlName'  => 'user',
        ];
        $pageName='user.create';
        return Helper::checkAdmin($pageName,$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUser $request
     * @return \Illuminate\Http\Response
     */
    public function store(request $request)
    {
        // $role = Role::where('id', $request['role_id'])->first();

        $request->validate([
            'name' => 'required|string',
            'title' => 'required|string',
            'department' => 'required|string',
            'company_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'user_name' => 'required|unique:users',
            'telephonenumber' => 'required|unique:users',
            'user_type' => 'required|in:0,1',
        ]);

        // Insert Data
        $password = str_random(10);
        $user           = new User();
        $user->name     = $request['name'];
        $user->email    = $request['email'];
        $user->user_name    = $request['user_name'];
        $user->company_name    = $request['company_name'];
        $user->title    = $request['title'];
        $user->department    = $request['department'];
        $user->user_type    = $request['user_type'];
        $user->telephonenumber    = $request['telephonenumber'];
        $user->password = Hash::make($password);

        $data = ['name' => $request['name'], 'username' => $request['user_name'], 'password' => $password];
        try {
            $user->save();
            // $user->roles()->attach($role);
            Mail::to($user->email)->later(now()->addSecond(2), new UserRegisterMail($data));

            return redirect('users')->with('status', "User successfully created and login credentials sent to $user->email. If you can not in your inbox, it is worth checking in your spam or junk mail section.");
        } catch (Exception $e) {
            return back()->withError($e)->withInput();
        }
    }

    public function makeadmin($userId, $status)
    {
        // Check user exit in user company table.
        $user_exist = UserCompany::where('user_id', $userId)->count();

        // Delete previous user company IDs
        if ($user_exist != 0) {
            UserCompany::where('user_id', $userId)->delete();
        }

        // Update user type.
        $updateUser            = User::find($userId);
        $updateUser->user_type = $status;
        $updateUser->save();

        $this->logStore('updated','user',"$updateUser->name( $updateUser->email ) Role updated.",'manage users');

        return redirect('users')->with('status', 'Successfully Updated!');
    }

    public function makeaudit(request $request)
    {
        $user_id     = $request['user_id'];
        $company_ids = $request['company_ids'];

        $user_exist = UserCompany::where('user_id', $user_id)->count();

        // Delete previous user company IDs
        if ($user_exist > 0) {
            UserCompany::where('user_id', $user_id)->delete();
        }

        // Insert New company of user.
        foreach ($company_ids as $key => $value) {
            $userCompany             = new UserCompany();
            $userCompany->user_id    = $user_id;
            $userCompany->company_id = $value;
            $userCompany->save();
        }

        // Update user type.
        $updateUser            = User::find($user_id);
        $updateUser->user_type = 2;
        $updateUser->save();

        $this->logStore('updated','user',"$updateUser->name( $updateUser->email ) Role/Company updated.",'manage users');

        return redirect('users')->with('status', 'Successfully Updated!');
    }

    public function makedpadmin(request $request)
    {
        $user_id     = $request['user_id'];
        $category_ids = $request['category_ids'];

        $user_exist = UserCategory::where('user_id', $user_id)->count();

        // Delete previous user company IDs
        if ($user_exist != 0) {
            UserCategory::where('user_id', $user_id)->delete();
        }

        // Insert New company of user.
        foreach ($category_ids as $key => $value) {
            $userCategory            = new UserCategory();
            $userCategory->user_id    = $user_id;
            $userCategory->category_id = $value;
            $userCategory->save();
        }

        // Update user type.
        $updateUser            = User::find($user_id);
        $updateUser->user_type = 3;
        $updateUser->save();

        $this->logStore('updated','user',"$updateUser->name( $updateUser->email ) Role/Department updated.",'manage users');

        return redirect('users')->with('status', 'Successfully Updated!');
    }


    public function category_list()
    {
        $category_list = DB::table('categorys')
            ->get()
            ->toArray();
        return response($category_list, 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Content-Type', 'application/json');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $userID int User ID
     * @return \Illuminate\Http\Response
     */
    public function edit($userID)
    {
        $data = [
            'pageTitle' => 'User Update',
            // 'userType'  => Role::orderBy('id', 'DESC')->pluck('name', 'id'),
            'ctrlName'  => 'user',
            'editData'  => User::where('id', '=', $userID)->firstOrFail(),
            // with('roles')->
        ];
        $pageName='user.edit';
        return Helper::checkAdmin($pageName,$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\StoreUser  $request
     * @param  $userID int User ID
     * @return \Illuminate\Http\Response
     */
    public function update(request $request, $userID)
    {
        if ($request->has('email')) {
            $request->validate([
                'name' => 'required|string',
                'title' => 'required|string',
                'department' => 'required|string',
                'company_name' => 'required|string',
                'email' => 'required|email|unique:users,email,'.$userID,
                'telephonenumber' => 'required|unique:users,telephonenumber,'.$userID,
                'user_type' => 'required|in:0,1',
            ]);
        }

        try {
            $update = user::find($userID);

            if ($request->has('name')) $update->name = $request['name'];
            if ($request->has('title')) $update->title = $request['title'];
            if ($request->has('department')) $update->department = $request['department'];
            if ($request->has('company_name')) $update->company_name = $request['company_name'];
            if ($request->has('email')) $update->email = $request['email'];
            if ($request->has('telephonenumber')) $update->telephonenumber = $request['telephonenumber'];
            if ($request->has('user_type')) $update->user_type = $request['user_type'];
            if ($request->has('is_active')) $update->is_active = $request['is_active'];

            $update->save();
        } catch (Exception $e) {
            return back()->withError('Value already exists.')->withInput();
        }
        return redirect('users')->with('status', 'Successfully User Updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $userID int User ID
     * @return \Illuminate\Http\Response
     */
    public function destroy($userID)
    {
        try {
            $user = User::findorfail($userID);
            $user->delete();
        } catch (Exception $e) {
            return back()->withError("Can't be deleted, have dependency.")->withInput();
        }
        return redirect('users')->with('status', 'Successfully User Deleted!');
    }

// public function ldapInfo(request $request){

// //  $usernames = Adldap::search()->users()->paginate(3000, 0);
    // //  // echo 'Total User for single request = '.count($usernames);
    // //   // print($usernames[0]);
    // //  $i = 0;
    // //  foreach($usernames as $result)
    // // {
    // //     $i++;
    // //     print_r($result->getAttributes('mail'));
    // //     echo '<br>';
    // // }
    // //  dd($i);

// //     exit();
    //     $usernames = Adldap::search()->groups()->get();
    //     $ls=1;
    //     $usersNmae = array();
    //     $userInsetArray=array();
    // foreach ($usernames as $key=> $value)
    // {
    //     $groupName=$value['attributes']['cn'][0];
    //     $sss=Adldap::search()->groups()->where('cn','=',"$groupName")->select(['member'])->first()->getAttribute('member');
    //     if(!empty($sss) && count($sss)!==0){
    //     for($i=0;$i<count($sss)-2;$i++){
    //          $habib=explode(",",$sss[$i]);
    //          $userName=str_replace("CN=","","$habib[0]");
    //         $as=Adldap::search()->users()->where('cn','=',"$userName")->first();
    //               if(isset($as['attributes']['cn'][0])){
    //            $name=$as['attributes']['cn'][0];
    //         }else{
    //              $name=NULL;
    //          }

//              if(isset($as['attributes']['mail'][0])){
    //            $email=$as['attributes']['mail'][0];
    //         }else{
    //              $email=NULL;
    //          }
    //              if(isset($as['attributes']['title'][0])){
    //            $title=$as['attributes']['title'][0];
    //         }else{
    //              $title=NULL;
    //          }
    //             if(isset($as['attributes']['department'][0])){
    //            $department=$as['attributes']['department'][0];
    //         }else{
    //              $department=NULL;
    //          }

//              if(isset($as['attributes']['telephonenumber'][0])){
    //            $telephonenumber=$as['attributes']['telephonenumber'][0];
    //         }else{
    //              $telephonenumber=NULL;
    //          }
    //                  if(isset($as['attributes']['company'][0])){
    //            $company=$as['attributes']['company'][0];
    //         }else{
    //              $company=NULL;
    //          }

//         $userNameInf=$as['attributes']['samaccountname'][0];
    //     if(!empty($userNameInf) && !in_array($userNameInf, $usersNmae) &&  isset($email)){
    //          $userNames=$as['attributes']['samaccountname'][0];
    //          $usersNmae[]=$as['attributes']['samaccountname'][0];

//     $checkUser= DB::table('users')->where('user_name','=',trim($userNames))->Orwhere('email','=',$email)->count();
    //      if($checkUser==0){
    //     $userInsetArray[]=['name'=>$name,'user_name'=>trim($userNames),'email'=>trim($email),'title'=>$title,'department'=>$department,'telephonenumber'=>$telephonenumber,'company_name'=>$company];
    //     }else{
    //     DB::table('users')->where('user_name','=',trim($email))
    //             ->update(['name'=>$name,'user_name'=>trim($userNames),'email'=>trim($email),'title'=>$title,'department'=>$department,'telephonenumber'=>$telephonenumber,'company_name'=>$company]);
    //         }

//       }
    //     }
    //   }
    // }
    // $insert=DB::table('users')->insert($userInsetArray);

// if($insert){
    //     return redirect('users')->with('status', 'Successfully Updated All AD User!');
    // }else{
    //    return redirect('users')->with('status', 'Nothing to update!');
    // }

// }

    public function ldapInfo(request $request)
    {
        $counts = $this->ldapService->importUsers();

        return redirect('users')->with('status', "Total {$counts['total_inserted_user']} user insert and {$counts['total_updated_user']} user updated Successfully!");

    }
}
