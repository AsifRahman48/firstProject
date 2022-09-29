<?php

namespace App\Http\Controllers;

use App\Ticket;
use App\TicketHistory;
use App\Category;
use App\SubCategory;
use App\Company;
use App\Traits\AuditLogTrait;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTicket;
use Mail;
use Auth;
use Storage;
use Artisan;
use DB;
use App\Http\Helpers\Helper;

class DashboardController extends Controller
{
    use AuditLogTrait;

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('role:Admin,Recommender,Initiator');
    }

    public function sendEmail()
    {

        $id = 11;
        $url = 'request/details/' . $id;
        $maildata = ['URL' => $url];
        Mail::send(['html' => 'emails.mail'], $maildata, function ($message) {
            $userInfoMailName = 'habib';
            $message->to("brain23@psgbd.com", $userInfoMailName)->subject('You have a new request notification');
            $message->from('noreplay@psgbd.com', 'Partex Star Group');
        });

    }

    /**
     * Show the application dashboard.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = ['pageTitle' => 'Dashboard'];
        return view('dashboard', compact('data'));
    }

    public function mail(Request $request)
    {


//         $to = "azmal@psgbd.com";
// $subject = "My subject";
// $txt = "Hello world!";
// $headers = "From: info@psgbd.com". "\r\n" .
// "CC: ahabib@bs-23.net";

// if(mail($to,$subject,$txt,$headers)){
//        echo 'habib';
// }
        $data = [
            'pageTitle' => 'Contact us'
        ];

        return view('contact_us', compact('data'));

    }

    public function bdBackup(Request $request)
    {
        $exists = Storage::allFiles(config('custom.settings.backup')); //Storage::disk('PartexStar');
        $data = [
            'pageTitle' => 'All Databse Backup File',
            'userList' => User::pluck('users.name', 'users.id'),
            'catList' => Category::where('active_date', '<=', date('Y-m-d'))
                ->Where(function ($query) {
                    $query->whereNull('deactive_date');
                    $query->orWhere('deactive_date', '>=', date('Y-m-d'));
                })
                ->orderBy('id', 'DESC')
                ->pluck('name', 'id'),
            'mthdName' => 'bdbackup',
            'ctrlName' => 'db',
            'file' => $exists
        ];
        $pageName = 'user.db_list';
        return Helper::checkAdmin($pageName, $data);
    }

    public function todayBackup(Request $request)
    {
        Artisan::call("backup:run", [
            '--only-db' => true,
            '--disable-notifications' => true
        ]);

        $directory = public_path('/DB_BACKUP/' . config('custom.settings.backup'));
        $files = scandir($directory, SCANDIR_SORT_DESCENDING);
        $newest_file = $files[0];

        $new_file_name = str_replace('.zip', '-only-db.zip', $newest_file);
        rename($directory . '/' . $newest_file, $directory . '/' . $new_file_name);

        $this->logStore('created', 'db backup', "Get backup only db.", 'backup manual');

        return redirect()->route('bdBackup')->with('status', 'Successfull');
    }

    public function withFileBackup(Request $request)
    {
        // $exists =Storage::allFiles('PartexStar');

        Artisan::call("backup:run", ['--disable-notifications' => true]);

        $this->logStore('created', 'full backup', "Get backup full application files.", 'backup manual');

        return redirect()->route('bdBackup')->with('status', 'Successfull');

    }

    public function backupDelete($folder, $id, Request $request)
    {
// echo $id;
// 	exit;
        // echo Storage::delete('PartexStar/2019-01-20-14-40-10.zip');
        // disk('DB_BACKUP')
        // $exists =Storage::allFiles('PartexStar');
        $location = config('custom.settings.backup') . '/' . trim($id);
        if (Storage::delete($location)) // if ($size=Storage::size('PartexStar/'.$id))
        {
            $this->logStore('deleted', 'backup', "Backup file removed.", 'backup manual');
            return redirect()->route('bdBackup')->with('status', 'Successfully Deleted');
        } else {

            // Artisan::call("backup:run");
            return redirect()->route('bdBackup')->with('status', 'Something is wrong ');
            // return redirect()->route('bd_backup')->with('status', 'Successfull');
        }

    }


// }
    public function AuditFullReport()
    {
        $data = [
            'pageTitle' => 'Audit Report Search',
            'userList' => User::pluck('users.name', 'users.id'),
            'catList' => Category::where('active_date', '<=', date('Y-m-d'))
                ->Where(function ($query) {
                    $query->whereNull('deactive_date');
                    $query->orWhere('deactive_date', '>=', date('Y-m-d'));
                })
                ->orderBy('id', 'DESC')
                ->pluck('name', 'id'),
            'mthdName' => 'report',
            'form' => 'formAudit'
        ];
        if (Auth::user()->user_type == 1) {
            $data['comList'] = Company::where('active_date', '<=', date('Y-m-d'))
                ->Where(function ($query) {
                    $query->whereNull('deactive_date');
                    $query->orWhere('deactive_date', '>=', date('Y-m-d'));
                })
                ->orderBy('name')
                ->pluck('name', 'id');

        } elseif (Auth::user()->user_type == 2) {
            $data['comList'] = DB::table('users_company')
                ->select('company_name.id as id', 'company_name.name as name')
                ->join('company_name', 'users_company.company_id', '=', 'company_name.id')
                ->where('users_company.user_id', '=', Auth::id())
                ->orderBy('name')
                ->pluck('name', 'id');

        } else {
            $data['comList'] = array();
        }
        return view('report.report_master', compact('data'));
    }

    public function ViewReport()
    {
        $data = [
            'pageTitle' => 'Recommendation/Approval Report Search',
            'userList' => User::pluck('users.name', 'users.id'),
            'catList' => Category::where('active_date', '<=', date('Y-m-d'))
                ->Where(function ($query) {
                    $query->whereNull('deactive_date');
                    $query->orWhere('deactive_date', '>=', date('Y-m-d'));
                })
                ->orderBy('id', 'DESC')
                ->pluck('name', 'id'),
            'mthdName' => 'report',
            'form' => 'form2'
        ];
        return view('report.report_master', compact('data'));


    }

    public function InitiatViewReport()
    {
        $data = [
            'pageTitle' => 'Initiated Request Report Search',
            'userList' => User::pluck('users.name', 'users.id'),
            'catList' => Category::where('active_date', '<=', date('Y-m-d'))
                ->Where(function ($query) {
                    $query->whereNull('deactive_date');
                    $query->orWhere('deactive_date', '>=', date('Y-m-d'));
                })
                ->orderBy('id', 'DESC')
                ->pluck('name', 'id'),
            'mthdName' => 'report',
            'form' => 'form1'
        ];
        return view('report.report_master', compact('data'));


    }
}
