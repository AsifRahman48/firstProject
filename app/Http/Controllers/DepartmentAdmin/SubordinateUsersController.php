<?php

namespace App\Http\Controllers\DepartmentAdmin;

use App\Http\Helpers\Helper;
use App\SubordinateUser;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SubordinateUsersController extends Controller
{
    public function index()
    {
        $searchTerm = strtolower(request('q'));

        $data = [
            'pageTitle' => 'Subordinate User List',
            'ctrlName' => 'subordinates',
            'listData' => User::whereNotNull('user_type')
                ->when(!empty($searchTerm), function ($query) use ($searchTerm) {
                    $query->whereRaw("LOWER(name) LIKE '%{$searchTerm}%'");
                    $query->orWhereRaw("LOWER(email) LIKE '%{$searchTerm}%'");
                    $query->orWhereRaw("LOWER(title) LIKE '%{$searchTerm}%'");
                    $query->orWhereRaw("LOWER(company_name) LIKE '%{$searchTerm}%'");
                    $query->orWhereRaw("LOWER(department) LIKE '%{$searchTerm}%'");
                    $query->orWhereRaw("LOWER(telephonenumber) LIKE '%{$searchTerm}%'");
                })
                ->where('id', '<>', auth()->id())
                ->select(['id', 'name', 'user_type', 'email', 'title', 'department', 'telephonenumber', 'company_name'])
                ->orderBy('name')->orderBy('id', 'DESC')->paginate(20)
        ];

        $pageName = 'department_admin.subordinates_users.index';
        return Helper::checkAdmin($pageName, $data);
    }

    public function searchSubordinateUser()
    {
        $search = request('term');
        $users = DB::table('users')
            ->where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhere('title', 'like', "%{$search}%")
            ->orWhere('department', 'like', "%{$search}%")
            ->orWhere('telephonenumber', 'like', "%{$search}%")
            ->whereNotIn('id', [request('parentUserID'), auth()->id()])
            ->take(10)
            ->get(['id', 'name', 'email']);

        return json_encode($users);
    }

    public function addSubordinateUser(Request $request)
    {
        DB::beginTransaction();
        try {
            $checkExists = SubordinateUser::where('user_id', $request->user_id)->count();
            if ($checkExists > 0) SubordinateUser::where('user_id', $request->user_id)->delete();

            $subordinates = [];
            foreach ($request->subordinate_users as $user){
                $subordinates[] = ['user_id' => $request->user_id, 'subordinate_user' => $user];
            }
            SubordinateUser::insert($subordinates);

            DB::commit();
            return redirect()->back()->with('status', "Successfully subordinate added.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function userSubordinateLists($user_id)
    {
        $subordinates = SubordinateUser::where('user_id', $user_id)->pluck('subordinate_user');
        $data = [
            'users' => User::whereIn('id', $subordinates)->get(['id', 'name', 'email']),
            'subordinates' => $subordinates
        ];

        return $data;
    }
}
