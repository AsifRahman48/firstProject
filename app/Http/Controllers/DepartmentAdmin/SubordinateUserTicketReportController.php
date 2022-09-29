<?php

namespace App\Http\Controllers\DepartmentAdmin;

use App\Category;
use App\Company;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubordinateUserTicketReportController
{
    protected function subOrdinateUsersTickets($subordinate_users){
        return DB::table('tickets')
            ->select('tickets.*','users.name as CreatorName','categorys.name as categorysName','sub_categorys.name as sub_categorysName','company_name.name as companyName')
            ->leftJoin('ticket_historys','tickets.id','=','ticket_historys.ticket_id')
            ->leftJoin('ticket_approve','tickets.id','=','ticket_approve.ticket_id')
            ->join('users','tickets.initiator_id','=','users.id')
            ->join('categorys','tickets.cat_id','=','categorys.id')
            ->join('sub_categorys','tickets.sub_cat_id','=','sub_categorys.id')
            ->join('company_name','tickets.company_id','=','company_name.id')
            ->whereIn('initiator_id', $subordinate_users->pluck('id'))
            ->where('tickets.created_at', '>=', Carbon::now()->subDays(30)->toDateTimeString())
            ->where('tickets.tStatus', '<>', 1)
            ->distinct()
            ->get();
    }

    protected function getStatusList(){
        $statusList[1]='Save as Draft ';
        $statusList[2]='Pending';
        $statusList[3]='Draft by Approver ';
        $statusList[4]='Approved ';
        $statusList[5]='Rejected ';
        $statusList[6]='Request for Info ';
        $statusList[7]='Forward';
        $statusList[8]='Appoved And Forward';
        $statusList[9]='Appoved And Acknowledgement';
        $statusList[10]='Disable';
        $statusList[11]='Pending';
        return $statusList;
    }

    public function index()
    {
        $subordinate_users = DB::table('subordinate_users')
            ->where('user_id', '=', auth()->id())
            ->join('users', 'subordinate_users.subordinate_user', '=', 'users.id')
            ->select('users.id', 'users.name', 'users.email')->get();
        $data = [
            'pageTitle' => 'Subordinate Users Report',
            'userList' => $subordinate_users,
            'tickets' => $this->subOrdinateUsersTickets($subordinate_users),
            'ticketStatus' => $this->getStatusList(),
            'catList' => Category::where('active_date', '<=', date('Y-m-d'))
                ->Where(function ($query) {
                    $query->whereNull('deactive_date');
                    $query->orWhere('deactive_date', '>=', date('Y-m-d'));
                })
                ->orderBy('id', 'DESC')
                ->pluck('name', 'id'),
            'mthdName' => 'subordinate_user_tickets_report',
            'form' => 'formAudit'
        ];
        if (auth()->user()->user_type == 1) {
            $data['comList'] = Company::where('active_date', '<=', date('Y-m-d'))
                ->Where(function ($query) {
                    $query->whereNull('deactive_date');
                    $query->orWhere('deactive_date', '>=', date('Y-m-d'));
                })
                ->orderBy('name')
                ->pluck('name', 'id');

        } elseif (auth()->user()->user_type == 2) {
            $data['comList'] = DB::table('users_company')
                ->select('company_name.id as id', 'company_name.name as name')
                ->join('company_name', 'users_company.company_id', '=', 'company_name.id')
                ->where('users_company.user_id', '=', Auth::id())
                ->orderBy('name')
                ->pluck('name', 'id');

        } else {
            $data['comList'] = array();
        }

        return view('department_admin.subordinate_user_tickets.index', compact('data'));
    }
}
