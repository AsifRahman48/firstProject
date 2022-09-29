<?php

namespace App\Http\Controllers;

use Adldap\Models\User;
use Adldap\Models\Auth;
use App\Http\Helpers\Helper;
use Illuminate\Http\Request;

class UpdateUserProfileController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware(['auth']);
        // $this->middleware('role:Admin');
    }
    public function edit($id)
    {
//            $data = [
//                'pageTitle' => 'User Update',
//                // 'userType'  => Role::orderBy('id', 'DESC')->pluck('name', 'id'),
//                'ctrlName'  => 'user',
//                'editData'  => \Illuminate\Support\Facades\Auth::user()->where('id', '=', $id)->firstOrFail(),
//                // with('roles')->
//            ];
//        $pageName='userUpdate.edit';
//        return Helper::checkAdmin($pageName,$data);


        $data=\Illuminate\Support\Facades\Auth::user()->where('id', '=', $id)->firstOrFail();
        return view('userUpdate.edit',compact('data'));
    }

    public function update(Request $request, $id)
    {
        if ($request->has('email')) {
            $request->validate([
                'name' => 'required|string',
                'title' => 'required|string',
                'department' => 'required|string',
                'company_name' => 'required|string',
                'email' => 'required|email|unique:users,email,'.$id,
                'telephonenumber' => 'required|unique:users,telephonenumber,'.$id,
            ]);
        }
        $update=\App\User::find($request->id);


            $update->name=$request->input('name');
            $update->title=$request->input('title');
            $update->department=$request->input('department');
            $update->company_name=$request->input('company_name');
            $update->email=$request->input('email');
            $update->telephonenumber=$request->input('telephonenumber');

        $update->save();

//        try {
//            $update = \App\User::find($id);
//;;
//            if ($request->has('name')) $update->name = $request['name'];
//            if ($request->has('title')) $update->title = $request['title'];
//            if ($request->has('department')) $update->department = $request['department'];
//            if ($request->has('company_name')) $update->company_name = $request['company_name'];
//            if ($request->has('email')) $update->email = $request['email'];
//            if ($request->has('telephonenumber')) $update->telephonenumber = $request['telephonenumber'];
//
//            $update->save();
//        } catch (Exception $e) {
//            return back()->withError('Value already exists.')->withInput();
//        }
        return redirect("/")->with('status', 'Successfully User Updated!');
    }
}
