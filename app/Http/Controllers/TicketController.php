<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Helpers\Helper;
use App\User;
use Illuminate\Support\Facades\Redirect;
use Exception;
use App\Ticket;
use App\Company;
use App\Category;
use App\SubCategory;
use App\UserVacation;
use App\TicketHistory;
use App\Traits\AuditLogTrait;
use Illuminate\Http\Request;
use App\Services\VacationService;
use App\TicketApprove as Approve;
use App\Http\Requests\StoreTicket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Input;

class TicketController extends Controller
{
    use AuditLogTrait;
	# History log user type
	const TYPE_INITIATOR = 0;
	const TYPE_RECOMMENDER = 1;
	const TYPE_APPROVER = 2;
	const TYPE_FORWARDED = 3;
	const TYPE_ACKNOWLEDGE = 4;
	# User got the requested ticket
	const TYPE_INFO_REQUESTED = 5;
	# User got requested information and ready for action
	const TYPE_INFO_REQUEST_BACK = 6;

	# History log user status
	const STATUS_INITIATED = 0;
	const STATUS_PENDING = 1;
	const STATUS_APPROVED = 2;
	const STATUS_FORWARD = 3;
	const STATUS_APPROVE_FORWARD = 4;
	const STATUS_APPROVE_ACKNOW = 5;
	const STATUS_ACKNOWLEDGED = 6;
	const STATUS_REJECTED = 7;
	const STATUS_REQUEST_INFO = 8;
	const STATUS_REQUEST_BACK = 9;




	private $vacationService;

    /**
     * Create a new controller instance.
     *
     * @param Request $request
     */
    public function __construct(
        Request $request,
        VacationService $vacationService
    ) {
        $this->middleware('auth');
        $this->vacationService = $vacationService;
        // $this->middleware('role:Initiator', ['only' => ['create', 'store', 'ticket_list']]);
    }

    /**
     * Create a new request.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'pageTitle'   => 'New Request',
            'CompanyName' => Company::where('active_date', '<=', date('Y-m-d'))
                ->Where(function ($query) {
                    $query->whereNull('deactive_date');
                    $query->orWhere('deactive_date', '>=', date('Y-m-d'));
                })->pluck('name', 'id'),
            'catList'     => Category::where('active_date', '<=', date('Y-m-d'))
                ->Where(function ($query) {
                    $query->whereNull('deactive_date');
                    $query->orWhere('deactive_date', '>=', date('Y-m-d'));
                })->orderBy('id', 'DESC')->pluck('name', 'id'),
            'userList'    => User::pluck('users.name', 'users.id'),
            'ctrlName'    => 'ticket',
            'mthdName'    => 'new',
            'userList2'   => User::get(),
        ];
        return view('ticket.create', compact('data'));
    }

    /**
     * @param $request
     * @param $size
     * @return bool
     *
     */
    private function validateFileSize($request,$size){

        $fileSize           = 0;
        if ($request->hasfile('tFile')) {

            foreach ($request->file('tFile') as $file) {

                $fileSize += $file->getClientSize();
            }

            $totalFilesize = number_format($fileSize / 1048576, 2);

            if ($totalFilesize > $size) {
                return false;
            }

            return true;

        }
        return true;

    }

    private function insertInitialTicket($inputData){
        $ticketRequest                = new Ticket();
        $ticketRequest->tReference_no = null;
        $ticketRequest->cat_id        = $inputData['cat_id'];
        $ticketRequest->sub_cat_id    = $inputData['sub_cat_id'];
        $ticketRequest->initiator_id  = Auth::id(); //$request->session()->get('userID')
        $ticketRequest->tSubject      = $inputData['tSubject'];
        $ticketRequest->tDescription  = $inputData['tDescription'];

        $ticketRequest->tStatus       = $inputData['tStatus'];

        $ticketRequest->now_ticket_at = reset($inputData['recommender_id']);
        $ticketRequest->priority      = $inputData['priority'];
        $ticketRequest->company_id    = $inputData['company_id'];
        $ticketRequest->save();
        return $ticketRequest->id;
    }

    private function generateUniqueReference($inputData,$ticketId){
        $companyInfo     = Company::where('id', '=', $inputData['company_id'])->select('short_name')->first();
        $categoryInfo    = Category::where('id', '=', $inputData['cat_id'])->select('name')->first();
        $subCategoryInfo = SubCategory::where('id', '=', $inputData['sub_cat_id'])->select('name')->first();
        $referenceNo  = $companyInfo->short_name . '-' . $categoryInfo->name . '-' . $subCategoryInfo->name . '-' . date('mY') . $ticketId;
        return $referenceNo;
    }

    private function uploadFile($request,$ticketid){
        $fileData = array();
        foreach ($request->file('tFile') as $file) {
            $fileName        = Auth::id() . '-' . time() . '-' . $file->getClientOriginalName();
            $fileType        = $file->getClientOriginalExtension();
            $destinationPath = public_path('/upload/ticket_file/' . date('Y'));
            $file->move($destinationPath, $fileName);
            $folder     = 'upload/ticket_file/' . date('Y');
            $fileData[] = ['ticket_id' => $ticketid, 'file_name' => $fileName, 'file_type' => $fileType, 'folder' => $folder];

        }
        DB::table('tickets_files')->insert($fileData);

    }
    private function prepareHistoryData($id,$name,$type,$type_id,$status,$status_id,$action,$date){

        return [
            'user_id'     =>$id,
            'user_name'   => $name,
            'user_type'   => $type,
            'user_type_id'   => $type_id,
            'user_status' => $status,
            'user_status_id' => $status_id,
            'user_action' => $action,
            'date'        => $date,
        ];

    }

    private function insertHistory(&$history,$data){
       $history[] = $data;
    }

    private function recommenderAction($recommenders,&$history,$ticket_id){
        $recommenderInfo=[];
        foreach($recommenders as $recommender){
                $recommenderInfo[] = ['ticket_id' => $ticket_id, 'user_id' => $recommender, 'user_type' => 1];
                $user          = User::where('id', $recommender)->first();
                $recommenderData   = $this->prepareHistoryData($recommender,$user->name,'Recommender',self::TYPE_RECOMMENDER,'Pending',self::STATUS_PENDING,0,'');
                $this->insertHistory($history,$recommenderData);
        }
        DB::table('ticket_approve')->insert($recommenderInfo);


    }

    private function approverAction($approvers,&$history,$ticket_id){
        $approverInfo=[];
        foreach ($approvers as $approver){
            $approverInfo[] = ['ticket_id' => $ticket_id, 'user_id' => $approver, 'user_type' => 2];
            $user          = User::where('id', $approver)->first();
            $approverData = $this->prepareHistoryData($approver,$user->name,'Approver',self::TYPE_APPROVER,'Pending',self::STATUS_PENDING,0,'');
            $this->insertHistory($history,$approverData);
        }
        DB::table('ticket_approve')->insert($approverInfo);

    }

    private function initiatorAction(&$history){
        $initiatorData = $this->prepareHistoryData(Auth::id(),Auth::user()->name,'Initiator',self::TYPE_INITIATOR,'Initiated',self::STATUS_INITIATED,1,date('d-m-Y h:i:s a'));
        $this->insertHistory($history,$initiatorData);
    }

    private function validateSelection($recommenders,$approvers,&$redirect_msg_error){
        if(count(array_intersect($recommenders,$approvers))>0 || in_array(Auth::id(),$recommenders) || in_array(Auth::id(),$approvers)){
            $redirect_msg_error = "Approver and Recommender can not be the same user";
            return false;
        }
        return true;
    }

    private function nextMail($inputData,$ticket_id,$referenceNo){
        if($inputData['tStatus'] !=1 )
        {
            $userInfoMail = User::find(reset($inputData['recommender_id']));
            $url = url('request/details/') .'/'. $ticket_id;
            $maildata = array(
                'URL'           => $url,
                'name'          => !empty($userInfoMail->name) ? $userInfoMail->name : ' ',
                'onlySubject'   => !empty($inputData['tSubject']) ? $inputData['tSubject'] : ' ',
                'tReference_no' => !empty($referenceNo) ? $referenceNo : ' ',
            );

            try {
                Mail::send(['html' => 'emails.mail'], $maildata, function ($message) use ($userInfoMail) {
                    if (empty($userInfoMail->name)) {
                        $userInfoMailName = '';
                    } else {
                        $userInfoMailName = $userInfoMail->name;
                    }

                    $message->to("$userInfoMail->email", "$userInfoMailName")->subject('You have a new request notification');
                    $message->from('ams.noreply@psgbd.com', 'AMS Notification System');
                });
            } catch (Exception $e) {
                #continue
            }
        }
    }

    public function getUsersForCheckVacation($inputData)
    {
        $users = array_merge($inputData['recommender_id'], $inputData['approver_id']);

        foreach($users as $userId) {
            // check user is on leave or not.
            // If on leave push the next forward user in array and check for this user as well
            while(true) {
                $userId = $userId;
                $forwardUser = UserVacation::where('user_id', $userId)->where('status', 'submitted')->latest()->first();
                if(!empty($forwardUser)
//                    && $this->vacationService->checkUserHasSetVacationOrNot($forwardUser['user_id'])
                    && $forwardUser->to_date > Carbon::now()->format('Y-m-d')
                    && !in_array($forwardUser['forward_user_id'], $users))
                {
                    array_push($users, $forwardUser['forward_user_id']);
                    $userId = $forwardUser['forward_user_id'];
                } else {
                    break;
                }
            }
        }

        return $users;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTicket $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $redirect_msg_wrong = '';
        //validation
        $request->validate([
            'company_id'   => 'required|max:10',
            'cat_id'       => 'required|max:10',
            'sub_cat_id'   => 'required',
            'tSubject'     => 'required',
            'tDescription' => 'required',

        ]);

        $inputData = $request->all();

        if(!$this->validateFileSize($request,10.00)){
            return redirect('/request/new')->withInput(Input::all())
                   ->with('error', 'if user try to add more than 10 MB file “You have exceed your limit');
        }

        $allUsers = $this->getUsersForCheckVacation($inputData);
        $isAllUsersOnVacation = true;

        foreach ($allUsers as $user) {
            if (!$this->vacationService->checkUserHasSetVacationOrNot($user)) {
                $isAllUsersOnVacation = false;
            }
        }

        if ($isAllUsersOnVacation) {
            return redirect('/request/new')->with('error', 'All Recommenders & Approvers are on leave. Please try later !');
        }

        if (empty($inputData['recommender_id']) or empty($inputData['approver_id'])) {
            return redirect('/request/new')->with('error', 'Mandatory Field is Missing');
        }

        if(!$this->validateSelection($inputData['recommender_id'], $inputData['approver_id'],  $redirect_msg_wrong)){
            return redirect('/request/new')->with('error', $redirect_msg_wrong);
        }

       DB::transaction(function () use ($inputData, $request) {

            $recommenders = $inputData['recommender_id'];
            $approvers = $inputData['approver_id'];

            $ticketId= $this->insertInitialTicket($inputData);

            if(isset($inputData['tStatus']) && $inputData['tStatus'] == 2){

                $referenceNo= $this->generateUniqueReference($inputData,$ticketId);
            }
            else{
                $referenceNo = null;
            }

            $history=[];
            $this->initiatorAction($history);
            $this->recommenderAction($recommenders,$history,$ticketId);
            $this->approverAction($approvers,$history,$ticketId);

            //---------------------------

            if ($request->hasfile('tFile')) {
                $this->uploadFile($request,$ticketId);
            }



            $update_thistory           = Ticket::find($ticketId);
            $update_thistory->tReference_no = $referenceNo;
            $update_thistory->thistory = json_encode($history);
//            $updateTicket = $update_thistory->save();
            $update_thistory->save();

            //$ticketData = Ticket::find($ticketId);
            if ($this->vacationService->checkUserHasSetVacationOrNot($update_thistory['now_ticket_at'])) {
                $this->vacationService->forwardTicket($update_thistory);
            } else {
                $this->nextMail($inputData,$ticketId,$referenceNo);
            }

           $status = $request->tStatus == 1 ? "draft" : "created";
           $this->logStore($status,'ticket',"$update_thistory->tSubject( $update_thistory->tReference_no ) ticket $status.",'requests new');
       });

        $redirect_to  = '/request/pending';
        $redirect_msg = 'Successfully new request submitted!';
        return redirect($redirect_to)->with('status', $redirect_msg);
    }

    public function inbox(Request $request)
    {

        $pageTitle = 'Inbox';
        $data      = [
            'pageTitle' => $pageTitle,
            'listData'  => Ticket::from('tickets as t')
                ->join('users as u', 'u.id', '=', 't.now_ticket_at')
                ->join('categorys as c', 'c.id', '=', 't.cat_id')
                ->join('sub_categorys as sc', 'sc.id', '=', 't.sub_cat_id')
            // ->where('t.initiator_id', '=', $request->session()->get('userID'))
                ->where([['t.initiator_id', '=', Auth::id()], ['t.tStatus', '!=', 1], ['t.is_delete', '=', 0], ['is_viewed', 0] ])
                ->orWhere([['t.now_ticket_at', '=', Auth::id()], ['t.tStatus', '!=', 1], ['t.is_delete', '=', 0], ['is_viewed', 0] ])
	            // ->where('t.tStatus', '!=', 1)
	            // ->where('t.is_delete', '=', 0)
                ->orderBy('t.id', 'DESC')
                ->get(['t.id', 't.tReference_no', 't.tSubject', 'u.name AS user_name', 'c.name AS cat_name', 'sc.name AS sub_cat_name', 't.tStatus', 't.initiator_id', 't.now_ticket_at', 't.thistory', 't.created_at', 't.updated_at', 't.is_delete']),
            'ctrlName'  => 'ticket',
            'mthdName'  => 'inbox',
        ];
        $getStatusList = Self::getStatusList();

        // dd($data);

        return view('ticket.inbox', compact('data'))->with(['StatusList' => $getStatusList]);

    }

    public function rejected(Request $request)
    {
        $pageTitle = 'Rejected Requests';
        $mthdName  = 'rejected';
        $tStatus   = 5;

        $data = [
            'pageTitle' => $pageTitle,
            'listData'  => Ticket::from('tickets as t')
                ->select('t.id', 't.tReference_no', 't.tSubject', 'u.name AS user_name', 'c.name AS cat_name', 'sc.name AS sub_cat_name', 't.tStatus', 't.initiator_id', 't.now_ticket_at', 't.thistory', 't.created_at', 't.updated_at')
                ->join('users as u', 'u.id', '=', 't.now_ticket_at')
                ->join('categorys as c', 'c.id', '=', 't.cat_id')
                ->join('sub_categorys as sc', 'sc.id', '=', 't.sub_cat_id')
                ->where([['t.initiator_id', '=', Auth::id()], ['is_viewed', 0]])
                ->where('t.tStatus', '=', $tStatus)
                ->orderBy('t.id', 'DESC')
                ->get(),
            'ctrlName'  => 'ticket',
            'mthdName'  => $mthdName,
        ];
        $getStatusList = Self::getStatusList();
        return view('ticket.rejected', compact('data'))->with(['StatusList' => $getStatusList]);
    }

    public function drafts(Request $request)
    {
        $pageTitle = 'Drafts Requests';
        $mthdName  = 'draft';
        $tStatus   = 1;

        $data = [
            'pageTitle' => $pageTitle,
            'listData'  => Ticket::from('tickets as t')
                ->select('t.id', 't.tReference_no', 't.tSubject', 'u.name AS user_name', 'c.name AS cat_name', 'sc.name AS sub_cat_name', 't.tStatus', 't.initiator_id', 't.now_ticket_at', 't.thistory', 't.created_at', 't.updated_at')
                ->join('users as u', 'u.id', '=', 't.now_ticket_at')
                ->join('categorys as c', 'c.id', '=', 't.cat_id')
                ->join('sub_categorys as sc', 'sc.id', '=', 't.sub_cat_id')
                ->where('t.initiator_id', '=', Auth::id())
                ->where('t.tStatus', '=', $tStatus)
                ->orderBy('t.id', 'DESC')
                ->get(),
            'ctrlName'  => 'ticket',
            'mthdName'  => $mthdName,
        ];
        $getStatusList = Self::getStatusList();
        return view('ticket.drafts', compact('data'))->with(['StatusList' => $getStatusList]);
    }

    public function approved(Request $request)
    {
        $pageTitle = 'Approved Requests';
        $mthdName  = 'approved';
        $tStatus   = 4;

        $data = [
            'pageTitle' => $pageTitle,
            'listData'  => Ticket::from('tickets as t')
                ->select('t.id', 't.tReference_no', 't.tSubject', 'u.name AS user_name', 'c.name AS cat_name', 'sc.name AS sub_cat_name', 't.tStatus', 't.initiator_id', 't.now_ticket_at', 't.thistory', 't.created_at', 't.updated_at')
                ->join('users as u', 'u.id', '=', 't.now_ticket_at')
                ->join('categorys as c', 'c.id', '=', 't.cat_id')
                ->join('sub_categorys as sc', 'sc.id', '=', 't.sub_cat_id')
                ->where('t.initiator_id', '=', Auth::id())
                ->where('t.now_ticket_at', '=', Auth::id())
                ->where('t.tStatus', '=', $tStatus)
                ->orderBy('t.id', 'DESC')
                ->get(),
            'ctrlName'  => 'ticket',
            'mthdName'  => $mthdName,
        ];
        $getStatusList = Self::getStatusList();
        return view('ticket.drafts', compact('data'))->with(['StatusList' => $getStatusList]);
    }

    public function requestInfo(Request $request)
    {
        $tStatus   = $request->route()->getAction()['tStatus'];
        $pageTitle = 'Requests for Information';
        $mthdName  = 'request_info';
        $data      = [
            'pageTitle' => $pageTitle,
            'listData'  => Ticket::from('tickets as t')
                ->select('t.id', 't.tReference_no', 't.tSubject', 'u.name AS user_name', 'c.name AS cat_name', 'sc.name AS sub_cat_name', 't.tStatus', 't.initiator_id', 't.now_ticket_at', 't.thistory', 't.created_at', 't.updated_at')
                ->join('users as u', 'u.id', '=', 't.now_ticket_at')
                ->join('categorys as c', 'c.id', '=', 't.cat_id')
                ->join('sub_categorys as sc', 'sc.id', '=', 't.sub_cat_id')
                ->where('t.now_ticket_at', '=', Auth::id())
                ->whereIn('t.tStatus', [6, 11])
            // ->orWhere('t.tStatus', '=', 11)
                ->orderBy('t.id', 'DESC')
                ->get(),
            'ctrlName'  => 'ticket',
            'mthdName'  => $mthdName,
        ];
        $getStatusList = Self::getStatusList();
        return view('ticket.requestInfo', compact('data'))->with(['StatusList' => $getStatusList]);

    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function ticket_list(Request $request)
    {
        $tStatus = $request->route()->getAction()['tStatus'];

        if ($tStatus == 2) {
            $pageTitle = 'Pending Requests';
            $mthdName  = 'pending';

        } elseif ($tStatus == 1) {
            $pageTitle = 'Draft Requests';
            $mthdName  = 'draft';

        } elseif ($tStatus == 6) {
            $pageTitle = 'Requests for Information';
            $mthdName  = 'request_info';

        } elseif ($tStatus == 5) {
            $pageTitle = 'Rejected Requests';
            $mthdName  = 'rejected';

        } else {
            $pageTitle = 'Approved Requests';
            $mthdName  = 'approved';
        }

        $data = [
            'pageTitle' => $pageTitle,
            'listData'  => Ticket::from('tickets as t')
                ->select('t.id', 't.tReference_no', 't.tSubject', 'u.name AS user_name', 'c.name AS cat_name', 'sc.name AS sub_cat_name', 't.tStatus', 't.initiator_id', 't.now_ticket_at', 't.thistory', 't.created_at', 't.updated_at')
                ->join('users as u', 'u.id', '=', 't.now_ticket_at')
                ->join('categorys as c', 'c.id', '=', 't.cat_id')
                ->join('sub_categorys as sc', 'sc.id', '=', 't.sub_cat_id')
            // ->where('t.initiator_id', '=', $request->session()->get('userID'))
                ->where([['t.initiator_id', '=', Auth::id()], ['is_viewed', 0]])
                ->whereIn('t.tStatus', [2, 6, 11, 7, 8, 10])
                ->orderBy('t.id', 'DESC')
                ->get(),
            // ->paginate(10, ['t.id', 't.tReference_no', 't.tSubject', 'u.name AS user_name', 'c.name AS cat_name', 'sc.name AS sub_cat_name','t.tStatus','t.thistory']),
            'ctrlName'  => 'ticket',
            'mthdName'  => $mthdName,
        ];
        $getStatusList = Self::getStatusList();

        return view('ticket.list', compact('data'))->with(['StatusList' => $getStatusList]);
    }

    public function ticket_details($id)
    {
        $previousUrl     = URL::previous();
        $TicketInfo      = Ticket::where('id', '=', $id)->where('is_delete', '=', 0)->first();

        # check deleted ticket
        if( empty($TicketInfo) ){
        	return redirect('/request/inbox')->with('error', 'Ticket not available!');
        }

        # Check drafts details page not show to other except initiator
        if( $TicketInfo->tStatus == 1 && $TicketInfo->initiator_id != Auth::id() ){
        	return redirect('/request/inbox')->with('error', 'You are not authorised to see this ticket!');
        }

        # Check other users details page not show except circle user.
        $ticket_avialable_for = [];
        # approver and recommender
        $approver_recommender = Approve::where('ticket_id', '=', $TicketInfo->id)->get(['user_id']);
        $ticket_avialable_for = $approver_recommender->toArray();
        # get history users
        $history_users = TicketHistory::where('ticket_id', '=', $TicketInfo->id)->get(['created_by']);
        if( !empty($history_users) ){
        	$ticket_avialable_for = array_merge($ticket_avialable_for, $history_users->toArray());
        }
        # now ticket at users
        $ticket_avialable_for = array_merge($ticket_avialable_for, [['now_ticket_at' => $TicketInfo->now_ticket_at]]);
        # Check initiator id
        $ticket_avialable_for = array_merge($ticket_avialable_for, [['initiator_id' => $TicketInfo->initiator_id]]);

        $ticket_avialable_for = array_map(function($values){
        	$array_values = array_values($values);
        	return $array_values[0];
        }, $ticket_avialable_for);

        $ticket_avialable_for_final = array_values(array_unique($ticket_avialable_for));

        $user = Auth::user();
        # User type 1 - Admin
        if( !in_array(Auth::id(), $ticket_avialable_for_final) && $user->user_type != 1 ){
        	return redirect('/request/inbox')->with('error', 'You are not authorised to see this ticket!');
        }
        # Check other users details page not show except circle user end.

        $subcatList      = SubCategory::where('cat_id', '=', $TicketInfo->cat_id)->get();
        $recommenderList = User::select('users.name', 'users.id')
            ->join('ticket_approve as TA', 'TA.user_id', '=', 'users.id')
            ->where('TA.user_type', '=', 1)
            ->where('TA.ticket_id', '=', $id)
            ->get();
        $approverList = User::select('users.name', 'users.id')
            ->join('ticket_approve as TA', 'TA.user_id', '=', 'users.id')
            ->where('TA.user_type', '=', 2)
            ->where('TA.ticket_id', '=', $id)
            ->get();
        // print_r($recommenderList);

        $PreviousComment = DB::table('ticket_historys as TH')
            ->join('users as UI', 'TH.action_to', '=', 'UI.id')
            ->select('TH.*', 'UI.name as User_name')
            ->where('TH.ticket_id', '=', $id)
            ->orderBy('TH.id', 'asc')
            ->get();

        // exit();
        $tStatus = $TicketInfo->tStatus;
        if ($tStatus == 2) {
            $pageTitle = 'Pending Requests';
            $mthdName  = 'pending';

        } elseif ($tStatus == 1) {
            $pageTitle = 'Draft Requests';
            $mthdName  = 'draft';

        } elseif ($tStatus == 6) {
            $pageTitle = 'Requests for Information';
            $mthdName  = 'request_info';

        } elseif ($tStatus == 11) {
            $pageTitle = 'Requests for Information';
            $mthdName  = 'request_info';

        } elseif ($tStatus == 5) {
            $pageTitle = 'Rejected Requests';
            $mthdName  = 'rejected';

        } else {
            $pageTitle = 'Approved Requests';
            $mthdName  = 'approved';
        }

        $attachmentFile = DB::table('tickets_files')->where('ticket_id', '=', $id)->where('is_delete', 0)->get();

        $data = [
            'pageTitle'       => 'Details View Request',
            'catList'         => Category::orderBy('id', 'DESC')->pluck('name', 'id'),
            'userList'        => User::pluck('users.name', 'users.id'),
            'ctrlName'        => 'ticket',
            'mthdName'        => $mthdName,
            'TicketInfo'      => $TicketInfo,
            'subcatList'      => $subcatList,
            'previousUrl'     => $previousUrl,
            'approverList'    => $approverList,
            'recommenderList' => $recommenderList,
            'attachmentFile'  => $attachmentFile,
            'PreviousComment' => $PreviousComment,
            'CompanyName'     => Company::where('active_date', '<=', date('Y-m-d'))
                ->Where(function ($query) {
                    $query->whereNull('deactive_date');
                    $query->orWhere('deactive_date', '>=', date('Y-m-d'));
                })->pluck('name', 'id'),
        ];



        if ($TicketInfo->initiator_id == Auth::id() && $TicketInfo->tStatus !== '4' && $TicketInfo->tStatus !== '6' && $TicketInfo->tStatus !== '11' && $TicketInfo->tStatus == '2') {
            // return view('ticket.edit_ticket', compact('data'));
            // return view('ticket.request_info_details', compact('data'));
            return view('ticket.details', compact('data'));
        } elseif ($TicketInfo->tStatus == '6' or $TicketInfo->tStatus == '11') {
            if ($TicketInfo->tStatus == '11' && $TicketInfo->initiator_id == Auth::id()) {

            	// dd($TicketInfo->tStatus);
                return view('ticket.details', compact('data'));
            } else {
                // return view('ticket.details', compact('data'));
                return view('ticket.request_info_details', compact('data'));
            }

        } else {
            return view('ticket.details', compact('data'));

        }

    }

    public function update_status(Request $request)
    {
        $input = $request->all();
        if (!empty($input['formAction'])) {
            $formAction = $input['formAction'];
            if ($formAction == '404') {
                $formAction = 9;
            }
            $id               = $input['id'];
            $result           = Ticket::find($id);
            $update           = Ticket::find($id);
            $update->comments = $input['Commentbox'];
            if ($formAction == '404') {
                $update->recommender_id = $input['forwardBy'];
            }
            $update->tStatus = $formAction;
            $update->save();
            $insert               = new TicketHistory();
            $insert->ticket_id    = $result->id;
            $insert->tStatus      = $formAction;
            $insert->request_from = $result->initiator_id;
            if ($formAction == '404') {
                $insert->request_to = $input['forwardBy'];
            } else {
                $insert->request_to = Auth::id();
            }
            $insert->comments     = $input['Commentbox'];
            $insert->tDescription = $input['tDescription'];
            $insert->save();

            if ($result->tStatus == 2) {
                $referenceNo  = uniqid('REF_');
                $redirect_to  = '/request/pending';
                $redirect_msg = 'Successfully request submitted!';
            } elseif ($result->tStatus == 1) {
                $referenceNo  = uniqid('REF_');
                $redirect_to  = '/request/drafts';
                $redirect_msg = 'Successfully request submitted!';
            } elseif ($result->tStatus == 6) {
                $referenceNo  = uniqid('REF_');
                $redirect_to  = '/request/request_info';
                $redirect_msg = 'Successfully request submitted!';
            } elseif ($result->tStatus == 5) {
                $referenceNo  = uniqid('REF_');
                $redirect_to  = '/request/rejected';
                $redirect_msg = 'Successfully request submitted!';
            } elseif ($result->tStatus == 4) {
                $referenceNo  = uniqid('REF_');
                $redirect_to  = '/request/approved';
                $redirect_msg = 'Successfully request submitted!';
            } else {
                $referenceNo  = null;
                $redirect_to  = '/';
                $redirect_msg = 'Request successfully.';
            }
            return redirect($input['previousUrl'])->with('status', $redirect_msg);

        }

    }

    private function UserTypeIdHistoryLog($history_user_type){

    	if( $history_user_type == 'Initiator' ){
    		$existing_uset_type_id = self::TYPE_INITIATOR;
    	}
    	elseif( $history_user_type == 'Recommender' ){
    		$existing_uset_type_id = self::TYPE_RECOMMENDER;
    	}
    	elseif ($history_user_type == 'Approver') {
    		$existing_uset_type_id = self::TYPE_APPROVER;
    	}
    	else{
    		$existing_uset_type_id = 101;
    	}

    	return $existing_uset_type_id;

    }

    private function UserStatusIdHistoryLog($history_user_status){

    	if( $history_user_status == 'Initiated' ){
    		$existing_user_status_id = self::STATUS_INITIATED;
    	}
    	elseif( $history_user_status == 'Pending' ){
    		$existing_user_status_id = self::STATUS_PENDING;
    	}
    	elseif ($history_user_status == 'Approved') {
    		$existing_user_status_id = self::STATUS_APPROVED;
    	}
    	else{
    		$existing_user_status_id = 102;
    	}

    	return $existing_user_status_id;

    }

    public function mailSend($inputData, $userInfoMail, $update_nextUser, $subMessage)
    {
            $id  = $inputData['id'];
            $url = url('request/details/').'/' . $id;
            $maildata = ['URL' => $url, 'name' => $userInfoMail->name, 'tReference_no' => $update_nextUser->tReference_no, 'onlySubject' => $update_nextUser->tSubject];

            try {
                Mail::send(['html' => 'emails.mail'], $maildata, function ($message) use ($userInfoMail, $subMessage) {
                    if (empty($userInfoMail->name)) {
                        $userInfoMailName = '';
                    } else {
                        $userInfoMailName = $userInfoMail->name;
                    }
                    $message->to("$userInfoMail->email", "$userInfoMailName")->subject($subMessage);
                    $message->from('ams.noreply@psgbd.com', 'AMS Notification System');
                });
            } catch (Exception $e) {
                # continue
            }
    }

    public function RequestStatusUpdate(Request $request)
    {
        $inputData = $request->all();
        if (!empty($inputData['formAction'])) {

            # check that the user is elegable to perform an action
            $ticket_info = Ticket::find($inputData['id']);

            if (empty($ticket_info) || $ticket_info->now_ticket_at != Auth::id()) {
                return redirect('/request/inbox')->with('error', 'You can not perform this action!');
            }

            // ================approved=================
            $formAction = $inputData['formAction'];
            if ($formAction == '4') {
                $formAction   = 4;
                DB::transaction(function () use ($inputData, $formAction) {
                    $statusUpdate = Approve::where('ticket_id', '=', $inputData['id'])->where('user_id', '=', Auth::id())->update(['action' => 1]);
                    if ($statusUpdate) {
                        $getFirstValues = Approve::where('ticket_id', '=', $inputData['id'])->where('action', '=', 0)->orderBy('id', 'asc')->first();
                        if (!empty($getFirstValues)) {
                            $update_nextUser                = Ticket::find($inputData['id']);
                            $update_nextUser->now_ticket_at = $getFirstValues->user_id;
                            $update_nextUser->save();

                            $userInfoMail = User::find($getFirstValues->user_id);
                            if (!empty($userInfoMail->email) && !$this->vacationService->checkUserHasSetVacationOrNot($update_nextUser['now_ticket_at'])) {
                                $subMessage = "You have a new request notification";
                                $this->mailSend($inputData, $userInfoMail, $update_nextUser, $subMessage);
                            }
                        } else {
                            $update_nextUser                = Ticket::find($inputData['id']);
                            $update_nextUser->now_ticket_at = $update_nextUser->initiator_id;
                            $update_nextUser->tStatus       = 4;
                            $update_nextUser->save();

                            # Send approved email to initiator after final approval
                            try{
                                $Subject='You request for '.$update_nextUser->tSubject.' has been approved ';
                                $ticNow=$update_nextUser->initiator_id;
                                $TicketID=$inputData['id'];
                                self::sendApproedMail($TicketID,$Subject,$ticNow);
                            }
                            catch(Exception $e){
                                # continue
                            }

                        }

                        $update_historys               = new TicketHistory();
                        $update_historys->ticket_id    = $inputData['id'];
                        $update_historys->tDescription = $inputData['Commentbox'];
                        $update_historys->tStatus      = $formAction;
                        $update_historys->action_to    = Auth::id();
                        $update_historys->created_by   = Auth::id();
                        $update_historys->save();

                    } else {
                        $getFirstValues = Approve::where('ticket_id', '=', $inputData['id'])->where('action', '=', 0)->orderBy('id', 'asc')->first();
                        if (!empty($getFirstValues)) {
                            $update_nextUser                = Ticket::find($inputData['id']);
                            $update_nextUser->now_ticket_at = $getFirstValues->user_id;
                            $update_nextUser->save();
                            $userInfoMail = User::find($getFirstValues->user_id);
                            if (!empty($userInfoMail->email) && !$this->vacationService->checkUserHasSetVacationOrNot($update_nextUser['now_ticket_at'])) {
                                $subMessage = "You have a new request notification";
                                $this->mailSend($inputData, $userInfoMail, $update_nextUser, $subMessage);
                            }
                        } else {
                            $update_nextUser                = Ticket::find($inputData['id']);
                            $update_nextUser->now_ticket_at = $update_nextUser->initiator_id;
                            $update_nextUser->tStatus       = 4;
                            $update_nextUser->save();

                        }
                        $update_historys               = new TicketHistory();
                        $update_historys->ticket_id    = $inputData['id'];
                        $update_historys->tDescription = $inputData['Commentbox'];
                        $update_historys->tStatus      = $formAction;
                        $update_historys->action_to    = Auth::id();
                        $update_historys->created_by   = Auth::id();
                        $update_historys->save();
                    }

                    $ticketData = Ticket::find($inputData['id']);

                    if (!empty($ticketData->thistory)) {
                        $log = json_decode($ticketData->thistory, true);

                        $logInfo = array();

                        foreach ($log as $key => $HistoryInfo) {

                            # For existing ticket
                            # Get user type id
                            if( isset($HistoryInfo['user_type']) && !empty($HistoryInfo['user_type']) ){
                                $existing_user_type_id = $this->UserTypeIdHistoryLog($HistoryInfo['user_type']);
                            }
                            else{
                                $existing_user_type_id = 101;
                            }
                            # Get user status id
                            if( isset($HistoryInfo['user_status']) && !empty($HistoryInfo['user_status']) ){
                                $existing_status_type_id = $this->UserStatusIdHistoryLog($HistoryInfo['user_status']);
                            }
                            else{
                                $existing_status_type_id = 102;
                            }

                            if ($HistoryInfo['user_id'] == Auth::id() && $HistoryInfo['user_action'] == 0) {

                                $logInfo[] = [
                                    'user_id' => $HistoryInfo['user_id'],
                                    'user_name' => $HistoryInfo['user_name'],
                                    'user_type' => $HistoryInfo['user_type'],
                                    'user_type_id' => !empty($HistoryInfo['user_type_id']) ? $HistoryInfo['user_type_id'] : $existing_user_type_id,
                                    'user_status' => 'Approved',
                                    'user_status_id' => self::STATUS_APPROVED,
                                    'user_action' => 1,
                                    'date' => date('d-m-Y H:i:s')
                                ];
                            } else {

                                $logInfo[] = [
                                    'user_id' => $HistoryInfo['user_id'],
                                    'user_name' => $HistoryInfo['user_name'],
                                    'user_type' => $HistoryInfo['user_type'],
                                    'user_type_id' => !empty($HistoryInfo['user_type_id']) ? $HistoryInfo['user_type_id'] : $existing_user_type_id,
                                    'user_status' => $HistoryInfo['user_status'],
                                    'user_status_id' => !empty($HistoryInfo['user_status_id']) ? $HistoryInfo['user_status_id'] : $existing_status_type_id,
                                    'user_action' => $HistoryInfo['user_action'],
                                    'date' => isset($HistoryInfo['date']) && !empty($HistoryInfo['date']) ? $HistoryInfo['date'] : ''
                                ];
                            }
                        }

                        $ticketData->thistory = json_encode($logInfo);
                        $ticketData->save();
                    }

                    if ($this->vacationService->checkUserHasSetVacationOrNot($ticketData['now_ticket_at'])) {
                        $this->vacationService->forwardTicket($ticketData);
                    };
                });
            }

            // ====================rejected=========================
            if ($formAction == '5') {
                $formAction   = 5;
                DB::transaction(function () use ($inputData, $formAction) {
                    $statusUpdate = Approve::where('ticket_id', '=', $inputData['id'])->where('user_id', '=', Auth::id())->update(['action' => 1]);
                    if ($statusUpdate) {
                        $getFirstValues                 = Approve::where('ticket_id', '=', $inputData['id'])->where('action', '=', 0)->orderBy('id', 'asc')->first();
                        $update_nextUser                = Ticket::find($inputData['id']);
                        $update_nextUser->now_ticket_at = $update_nextUser->initiator_id;
                        $update_nextUser->tStatus       = 5;
                        $update_nextUser->save();
                        $update_historys               = new TicketHistory();
                        $update_historys->ticket_id    = $inputData['id'];
                        $update_historys->tDescription = $inputData['Commentbox'];
                        $update_historys->tStatus      = $formAction;
                        $update_historys->action_to    = Auth::id();
                        $update_historys->created_by   = Auth::id();
                        $update_historys->save();
                    } else {
                        $getFirstValues                 = Approve::where('ticket_id', '=', $inputData['id'])->where('action', '=', 0)->orderBy('id', 'asc')->first();
                        $update_nextUser                = Ticket::find($inputData['id']);
                        $update_nextUser->now_ticket_at = $update_nextUser->initiator_id;
                        $update_nextUser->tStatus       = 5;
                        $update_nextUser->save();
                        $update_historys               = new TicketHistory();
                        $update_historys->ticket_id    = $inputData['id'];
                        $update_historys->tDescription = $inputData['Commentbox'];
                        $update_historys->tStatus      = $formAction;
                        $update_historys->action_to    = Auth::id();
                        $update_historys->created_by   = Auth::id();
                        $update_historys->save();
                    }
                    $userInfoMail = User::find($update_nextUser->initiator_id);
                    if (!empty($userInfoMail->email)) {
                        $subMessage = 'You request has been rejected';
                        $this->mailSend($inputData, $userInfoMail, $update_nextUser, $subMessage);
                    }

                    $udateTicketLog = Ticket::find($inputData['id']);
                    $log            = json_decode($udateTicketLog->thistory, true);
                    $logInfo        = array();
                    foreach ($log as $key => $HistoryInfo) {

                        # For existing ticket
                        # Get user type id
                        if( isset($HistoryInfo['user_type']) && !empty($HistoryInfo['user_type']) ){
                            $existing_user_type_id = $this->UserTypeIdHistoryLog($HistoryInfo['user_type']);
                        }
                        else{
                            $existing_user_type_id = 101;
                        }
                        # Get user status id
                        if( isset($HistoryInfo['user_status']) && !empty($HistoryInfo['user_status']) ){
                            $existing_status_type_id = $this->UserStatusIdHistoryLog($HistoryInfo['user_status']);
                        }
                        else{
                            $existing_status_type_id = 102;
                        }


                        if ($HistoryInfo['user_id'] == Auth::id() && $HistoryInfo['user_action'] == 0) {
                            $logInfo[] = [
                                'user_id' => $HistoryInfo['user_id'],
                                'user_name' => $HistoryInfo['user_name'],
                                'user_type' => $HistoryInfo['user_type'],
                                'user_type_id' => !empty($HistoryInfo['user_type_id']) ? $HistoryInfo['user_type_id'] : $existing_user_type_id,
                                'user_status' => 'Rejected',
                                'user_status_id' => self::STATUS_REJECTED,
                                'user_action' => 1,
                                'date' => date('d-m-Y H:i:s')
                            ];
                        } else {
                            $logInfo[] = [
                                'user_id' => $HistoryInfo['user_id'],
                                'user_name' => $HistoryInfo['user_name'],
                                'user_type' => $HistoryInfo['user_type'],
                                'user_type_id' => !empty($HistoryInfo['user_type_id']) ? $HistoryInfo['user_type_id'] : $existing_user_type_id,
                                'user_status' => $HistoryInfo['user_status'],
                                'user_status_id' => !empty($HistoryInfo['user_status_id']) ? $HistoryInfo['user_status_id'] : $existing_status_type_id,
                                'user_action' => $HistoryInfo['user_action'],
                                'date' => isset($HistoryInfo['date']) && !empty($HistoryInfo['date']) ? $HistoryInfo['date'] : ''
                            ];
                        }

                    }

                    $udateTicketLog->thistory = json_encode($logInfo);
                    $udateTicketLog->save();
                });
            }
            // ====================Request For Info=========================
            if ($formAction == '6') {
                $formAction = 6;

                if (empty($inputData['requestInfoBy'])) {
                    return redirect('/request/inbox')->with('error', 'You did not select any user for request.');
                }

                DB::transaction( function() use ($inputData, $formAction) {
                    $getFirstValues                 = Approve::where('ticket_id', '=', $inputData['id'])->where('action', '=', 0)->orderBy('id', 'asc')->first();
                    $update_nextUser                = Ticket::find($inputData['id']);
                    $update_nextUser->now_ticket_at = $inputData['requestInfoBy'];
                    $update_nextUser->tStatus       = $formAction;
                    $update_nextUser->save();
                    $update_historys               = new TicketHistory();
                    $update_historys->ticket_id    = $inputData['id'];
                    $update_historys->tDescription = $inputData['Commentbox'];
                    $update_historys->tStatus      = $formAction;
                    $update_historys->action_to    = Auth::id();
                    $update_historys->created_by   = Auth::id();
                    $update_historys->save();

                    $userInfoMail = User::find($inputData['requestInfoBy']);
                    if (!empty($userInfoMail->email) && !$this->vacationService->checkUserHasSetVacationOrNot($update_nextUser['now_ticket_at'])) {
                        $subMessage = 'You have a new request notification';
                        $this->mailSend($inputData, $userInfoMail, $update_nextUser, $subMessage);
                    }

                    $ticketData = Ticket::find($inputData['id']);
                    $log            = json_decode($ticketData->thistory, true);
                    $logInfo        = array();

                    foreach ($log as $key => $HistoryInfo) {

                        # For existing ticket
                        # Get user type id
                        if( isset($HistoryInfo['user_type']) && !empty($HistoryInfo['user_type']) ){
                            $existing_user_type_id = $this->UserTypeIdHistoryLog($HistoryInfo['user_type']);
                        }
                        else{
                            $existing_user_type_id = 101;
                        }
                        # Get user status id
                        if( isset($HistoryInfo['user_status']) && !empty($HistoryInfo['user_status']) ){
                            $existing_status_type_id = $this->UserStatusIdHistoryLog($HistoryInfo['user_status']);
                        }
                        else{
                            $existing_status_type_id = 102;
                        }


                        if ($this->vacationService->checkUserHasSetVacationOrNot($ticketData['now_ticket_at'])) {
                            $user_action = 0;
                        } else {
                            $user_action = 1;
                        }


                        if ( $HistoryInfo['user_id'] == Auth::id() && $HistoryInfo['user_action'] == 0 ) {

                            $logInfo[] = [
                                'user_id'     => $HistoryInfo['user_id'],
                                'user_name'   => $HistoryInfo['user_name'],
                                'user_type'   => $HistoryInfo['user_type'],
                                'user_type_id'   => !empty($HistoryInfo['user_type_id']) ? $HistoryInfo['user_type_id'] : $existing_user_type_id,
                                'user_status' => 'Request For Info',
                                'user_status_id' => self::STATUS_REQUEST_INFO,
                                'user_action' => $user_action,
                                'date'        => date('d-m-Y h:i:s a'),
                            ];

                            $userInfo = User::where('id', $inputData['requestInfoBy'])->first();

                            # Request info from initiator
                            if ( $userInfo->id == $ticketData->initiator_id ){

                                $logInfo[] = [
                                    'user_id'     => $userInfo->id,
                                    'user_name'   => $userInfo->name,
                                    'user_type'   => 'Initiatior',
                                    'user_type_id'   => self::TYPE_INITIATOR,
                                    'user_status' => 'Pending',
                                    'user_status_id' => self::STATUS_PENDING,
                                    'user_action' => $user_action,
                                    'date'        => '',
                                ];
                            }
                            #request info from other user
                            else{

                                $logInfo[] = [
                                    'user_id'     => $userInfo->id,
                                    'user_name'   => $userInfo->name,
                                    'user_type'   => 'Request For Info User',
                                    'user_type_id'   => self::TYPE_INFO_REQUESTED,
                                    'user_status' => 'Pending',
                                    'user_status_id' => self::STATUS_PENDING,
                                    'user_action' => $user_action,
                                    'date'        => '',
                                ];
                            }



                        } else {
                            if (isset($HistoryInfo['date'])) {
                                $habibdate = $HistoryInfo['date'] ? $HistoryInfo['date'] : '';
                            } else {
                                $habibdate = '';
                            }

                            $logInfo[] = [
                                'user_id'     => $HistoryInfo['user_id'],
                                'user_name'   => $HistoryInfo['user_name'],
                                'user_type'   => $HistoryInfo['user_type'],
                                'user_type_id'   => !empty($HistoryInfo['user_type_id']) ? $HistoryInfo['user_type_id'] : $existing_user_type_id,
                                'user_status' => $HistoryInfo['user_status'],
                                'user_status_id' => !empty($HistoryInfo['user_status_id']) ? $HistoryInfo['user_status_id'] : $existing_status_type_id,
                                'user_action' => $HistoryInfo['user_action'],
                                'date'        => $habibdate,
                            ];

                        }

                    }

                    $ticketData->thistory = json_encode($logInfo);
                    $ticketData->save();

                    if ($this->vacationService->checkUserHasSetVacationOrNot($ticketData['now_ticket_at'])) {
                        $this->vacationService->forwardTicket($ticketData);
                    };
                });
            }

            // ====================Request For Info Back=========================
            if ($formAction == '11') {
                $formAction = 11;



                $getRequestInfoByFirstValues = TicketHistory::where('ticket_id', '=', $inputData['id'])->where('tStatus', '=', '6')->orderBy('id', 'DESC')->first();
                // $getRequestInfoByFirstValues=TicketHistory::where('ticket_id','=',$inputData['id'])->orderBy('id', 'DESC')->skip(1)->take(1)->get();
                $getRequestInfoByFirstValues->created_by;
                $update_nextUser                = Ticket::find($inputData['id']);
                $update_nextUser->now_ticket_at = $getRequestInfoByFirstValues->created_by;
                $update_nextUser->tStatus       = $formAction;
                $update_nextUser->save();
                $update_historys               = new TicketHistory();
                $update_historys->ticket_id    = $inputData['id'];
                $update_historys->tDescription = $inputData['Commentbox'];
                $update_historys->tStatus      = $formAction;
                $update_historys->action_to    = Auth::id();
                $update_historys->created_by   = Auth::id();
                $update_historys->save();

                $userInfoMail = User::find($getRequestInfoByFirstValues->created_by);

                if (!empty($userInfoMail->email) && !$this->vacationService->checkUserHasSetVacationOrNot($update_nextUser['now_ticket_at'])) {
                    $subMessage = "You have a new request notification";
                    $this->mailSend($inputData, $userInfoMail, $update_nextUser, $subMessage);
                }

                # Update ticket history
                $ticketData             = Ticket::find($inputData['id']);
                $log                        = json_decode($ticketData->thistory, true);
                $logInfo                    = array();

                foreach ($log as $key => $HistoryInfo) {

                	# For existing ticket
                	# Get user type id
                	if( isset($HistoryInfo['user_type']) && !empty($HistoryInfo['user_type']) ){
                		$existing_user_type_id = $this->UserTypeIdHistoryLog($HistoryInfo['user_type']);
                	}
                	else{
                		$existing_user_type_id = 101;
                	}
                	# Get user status id
                	if( isset($HistoryInfo['user_status']) && !empty($HistoryInfo['user_status']) ){
                		$existing_status_type_id = $this->UserStatusIdHistoryLog($HistoryInfo['user_status']);
                	}
                	else{
                		$existing_status_type_id = 102;
                	}

                    if ( $HistoryInfo['user_id'] == Auth::id() ) {

                    	# Check for request info back pending
                    	if( !empty($HistoryInfo['user_status_id']) && $HistoryInfo['user_status_id'] == self::STATUS_PENDING ){

                    		# Initiator request info back
                    		if( !empty($HistoryInfo['user_type_id']) && $HistoryInfo['user_type_id'] == self::TYPE_INITIATOR ){

                    			$logInfo[] = [
                    			    'user_id'     => $HistoryInfo['user_id'],
                    			    'user_name'   => $HistoryInfo['user_name'],
                    			    'user_type'   => $HistoryInfo['user_type'],
                    			    'user_type_id'   => !empty($HistoryInfo['user_type_id']) ? $HistoryInfo['user_type_id'] : $existing_user_type_id,
                    			    'user_status' => 'Requested Info Back',
                    			    'user_status_id' => self::STATUS_REQUEST_BACK,
                    			    'user_action' => 1,
                    			    'date'        => date('d-m-Y h:i:s a'),
                    			];


                    			# Sending info back to user for action
                    			$userInfo = User::where( 'id', $getRequestInfoByFirstValues->created_by )->first();
                    			$user_type = Approve::where('ticket_id', '=', $inputData['id'])->where('user_id', '=', $userInfo->id)->first();

                    			if( !empty($user_type->user_type) && $user_type->user_type == self::TYPE_RECOMMENDER ){
                    				$user_type_name = 'Recommender';
                    			}
                    			elseif( !empty($user_type->user_type) && $user_type->user_type == self::TYPE_APPROVER ){
                    				$user_type_name = 'Approver';
                    			}
                    			else{
                    				$user_type_name = 'Request Info Back User';
                    			}

                				$logInfo[] = [
                				    'user_id'     => $userInfo->id,
                				    'user_name'   => $userInfo->name,
                				    'user_type'   => $user_type_name,
                				    'user_type_id'   => self::TYPE_INFO_REQUEST_BACK,
                				    'user_status' => 'Pending',
                				    'user_status_id' => self::STATUS_PENDING,
                				    'user_action' => 0,
                				    'date'        => '',
                				];



                    		}
                    		# Other user request info back
                    		else{
                    			$logInfo[] = [
                    			    'user_id'     => $HistoryInfo['user_id'],
                    			    'user_name'   => $HistoryInfo['user_name'],
                    			    'user_type'   => $HistoryInfo['user_type'],
                    			    'user_type_id'   => !empty($HistoryInfo['user_type_id']) ? $HistoryInfo['user_type_id'] : $existing_user_type_id,
                    			    'user_status' => 'Requested Info Back',
                    			    'user_status_id' => self::STATUS_REQUEST_BACK,
                    			    'user_action' => 1,
                    			    'date'        => date('d-m-Y h:i:s a'),
                    			];



                    			# Sending info back to user for action
                    			$userInfo = User::where( 'id', $getRequestInfoByFirstValues->created_by )->first();
                    			$user_type = Approve::where('ticket_id', '=', $inputData['id'])->where('user_id', '=', $userInfo->id)->first();

                    			if( !empty($user_type->user_type) && $user_type->user_type == self::TYPE_RECOMMENDER ){
                    				$user_type_name = 'Recommender';
                    			}
                    			elseif( !empty($user_type->user_type) && $user_type->user_type == self::TYPE_APPROVER ){
                    				$user_type_name = 'Approver';
                    			}
                    			else{
                    				$user_type_name = 'Request Info Back User';
                    			}

                				$logInfo[] = [
                				    'user_id'     => $userInfo->id,
                				    'user_name'   => $userInfo->name,
                				    'user_type'   => $user_type_name,
                				    'user_type_id'   => self::TYPE_INFO_REQUEST_BACK,
                				    'user_status' => 'Pending',
                				    'user_status_id' => self::STATUS_PENDING,
                				    'user_action' => 0,
                				    'date'        => '',
                				];


                    		}


                    	}
                    	else{

                    		if (isset($HistoryInfo['date'])) {
                    		    $habibdate = $HistoryInfo['date'] ? $HistoryInfo['date'] : '';
                    		} else {
                    		    $habibdate = '';
                    		}

                    		$logInfo[] = [
                    		    'user_id'     => $HistoryInfo['user_id'],
                    		    'user_name'   => $HistoryInfo['user_name'],
                    		    'user_type'   => $HistoryInfo['user_type'],
                    		    'user_type_id'   => !empty($HistoryInfo['user_type_id']) ? $HistoryInfo['user_type_id'] : $existing_user_type_id,
                    		    'user_status' => $HistoryInfo['user_status'],
                    		    'user_status_id' => !empty($HistoryInfo['user_status_id']) ? $HistoryInfo['user_status_id'] : $existing_status_type_id,
                    		    'user_action' => $HistoryInfo['user_action'],
                    		    'date'        => $habibdate,
                    		];
                    	}

                    }
                    else {

                        if (isset($HistoryInfo['date'])) {
                            $habibdate = $HistoryInfo['date'] ? $HistoryInfo['date'] : '';
                        } else {
                            $habibdate = '';
                        }

                        $logInfo[] = [
                            'user_id'     => $HistoryInfo['user_id'],
                            'user_name'   => $HistoryInfo['user_name'],
                            'user_type'   => $HistoryInfo['user_type'],
                            'user_type_id'   => !empty($HistoryInfo['user_type_id']) ? $HistoryInfo['user_type_id'] : $existing_user_type_id,
                            'user_status' => $HistoryInfo['user_status'],
                            'user_status_id' => !empty($HistoryInfo['user_status_id']) ? $HistoryInfo['user_status_id'] : $existing_status_type_id,
                            'user_action' => $HistoryInfo['user_action'],
                            'date'        => $habibdate,
                        ];

                    }

                }

                $ticketData->thistory = json_encode($logInfo);
                $ticketData->save();

                if($this->vacationService->checkUserHasSetVacationOrNot($ticketData['now_ticket_at'])) {
                    $this->vacationService->forwardTicket($ticketData);
                }

            }

            // ====================Forward=========================
            if ($formAction == '7') {
                $formAction = 7;
                DB::transaction(function () use ($inputData, $formAction) {
                    # Check empty forwarded user
                    if (empty($inputData['forwardUser'])) {
                        return redirect('/request/inbox')->with('error', 'You did not select any user to forward');
                    }

                    # Check that user should not forward ticket to Initiator
                    $initiator_forwarded = Ticket::where('id', '=', $inputData['id'])->where('initiator_id', '=', $inputData['forwardUser'])->first();
                    if( !empty($initiator_forwarded) ){
                        # Initiator and forwared user is the same
                        return redirect('/request/inbox')->with('error', 'You can not forward ticket to Initiatior.');
                    }

                    $statusUpdate = Approve::where('ticket_id', '=', $inputData['id'])->where('user_id', '=', Auth::id())->update(['action' => 1]);
                    if ($statusUpdate) {
                        $getFirstValues                 = Approve::where('ticket_id', '=', $inputData['id'])->where('action', '=', 0)->orderBy('id', 'asc')->first();
                        $update_nextUser                = Ticket::find($inputData['id']);
                        $update_nextUser->now_ticket_at = $inputData['forwardUser'];
                        $update_nextUser->tStatus       = 7;
                        $update_nextUser->save();
                        $update_historys               = new TicketHistory();
                        $update_historys->ticket_id    = $inputData['id'];
                        $update_historys->tDescription = $inputData['Commentbox'];
                        $update_historys->tStatus      = $formAction;
                        $update_historys->action_to    = Auth::id();
                        $update_historys->created_by   = Auth::id();
                        $update_historys->save();
                    } else {
                        $getFirstValues                 = Approve::where('ticket_id', '=', $inputData['id'])->where('action', '=', 0)->orderBy('id', 'asc')->first();
                        $update_nextUser                = Ticket::find($inputData['id']);
                        $update_nextUser->now_ticket_at = $inputData['forwardUser'];
                        $update_nextUser->tStatus       = 7;
                        $update_nextUser->save();
                        $update_historys               = new TicketHistory();
                        $update_historys->ticket_id    = $inputData['id'];
                        $update_historys->tDescription = $inputData['Commentbox'];
                        $update_historys->tStatus      = $formAction;
                        $update_historys->action_to    = Auth::id();
                        $update_historys->created_by   = Auth::id();
                        $update_historys->save();
                    }

                    $userInfoMail = User::find($inputData['forwardUser']);
                    if (!empty($userInfoMail->email) && !$this->vacationService->checkUserHasSetVacationOrNot($update_nextUser['now_ticket_at'])) {
                        $subMessage = 'You have a new request notification';
                        $this->mailSend($inputData, $userInfoMail, $update_nextUser, $subMessage);
                    }

                    $ticketData = Ticket::find($inputData['id']);
                    $log            = json_decode($ticketData->thistory, true);
                    $logInfo        = array();
                    foreach ($log as $key => $HistoryInfo) {

                        # For existing ticket
                        # Get user type id
                        if( isset($HistoryInfo['user_type']) && !empty($HistoryInfo['user_type']) ){
                            $existing_user_type_id = $this->UserTypeIdHistoryLog($HistoryInfo['user_type']);
                        }
                        else{
                            $existing_user_type_id = 101;
                        }
                        # Get user status id
                        if( isset($HistoryInfo['user_status']) && !empty($HistoryInfo['user_status']) ){
                            $existing_status_type_id = $this->UserStatusIdHistoryLog($HistoryInfo['user_status']);
                        }
                        else{
                            $existing_status_type_id = 102;
                        }



                        if ($HistoryInfo['user_id'] == Auth::id() && $HistoryInfo['user_action'] == 0) {

                            $logInfo[] = [
                                'user_id' => $HistoryInfo['user_id'],
                                'user_name' => $HistoryInfo['user_name'],
                                'user_type' => $HistoryInfo['user_type'],
                                'user_type_id' => !empty($HistoryInfo['user_type_id']) ? $HistoryInfo['user_type_id'] : $existing_user_type_id,
                                'user_status' => 'Forward',
                                'user_status_id' => self::STATUS_FORWARD,
                                'user_action' => 1,
                                'date' => date('d-m-Y H:i:s')
                            ];

                            $userInfo  = User::where('id', $inputData['forwardUser'])->first();

                            $logInfo[] = [
                                'user_id' => $userInfo->id,
                                'user_name' => $userInfo->name,
                                'user_type' => 'Forward User',
                                'user_type_id' => self::TYPE_FORWARDED,
                                'user_status' => 'Pending',
                                'user_status_id' => self::STATUS_PENDING,
                                'user_action' => 0,
                                'date' => ''
                            ];

                        } else {
                            $logInfo[] = [
                                'user_id' => $HistoryInfo['user_id'],
                                'user_name' => $HistoryInfo['user_name'],
                                'user_type' => $HistoryInfo['user_type'],
                                'user_type_id' => !empty($HistoryInfo['user_type_id']) ? $HistoryInfo['user_type_id'] : $existing_user_type_id,
                                'user_status' => $HistoryInfo['user_status'],
                                'user_status_id' => !empty($HistoryInfo['user_status_id']) ? $HistoryInfo['user_status_id'] : $existing_status_type_id,
                                'user_action' => $HistoryInfo['user_action'],
                                'date' => isset($HistoryInfo['date']) && !empty($HistoryInfo['date']) ? $HistoryInfo['date'] : ''
                            ];
                        }

                    }

                    $ticketData->thistory = json_encode($logInfo);
                    $ticketData->save();

                    if ($this->vacationService->checkUserHasSetVacationOrNot($ticketData['now_ticket_at'])) {
                        $this->vacationService->forwardTicket($ticketData);
                    };
                });
            }
            // ====================Appoved And Forward=========================
            if ($formAction == '504') {
                $formAction = 8;

               DB::transaction(function () use ($inputData, $formAction) {
                    if (empty($inputData['forwardBy'])) {
                        return redirect('/request/inbox')->with('error', 'You did not select any user to forward');
                    }

                    # Check that user should not forward ticket to Initiator
                    $initiator_forwarded = Ticket::where('id', '=', $inputData['id'])->where('initiator_id', '=', $inputData['forwardBy'])->first();
                    if( !empty($initiator_forwarded) ){
                        # Initiator and forwared user is the same
                        return redirect('/request/inbox')->with('error', 'You can not forward ticket to Initiatior.');
                    }


                    $statusUpdate = Approve::where('ticket_id', '=', $inputData['id'])->where('user_id', '=', Auth::id())->update(['action' => 1]);
                    if ($statusUpdate) {
                        $getFirstValues                 = Approve::where('ticket_id', '=', $inputData['id'])->where('action', '=', 0)->orderBy('id', 'asc')->first();
                        $update_nextUser                = Ticket::find($inputData['id']);
                        $update_nextUser->now_ticket_at = $inputData['forwardBy'];
                        $update_nextUser->tStatus       = 7;
                        $update_nextUser->save();
                        $update_historys               = new TicketHistory();
                        $update_historys->ticket_id    = $inputData['id'];
                        $update_historys->tDescription = $inputData['Commentbox'];
                        $update_historys->tStatus      = $formAction;
                        $update_historys->action_to    = Auth::id();
                        $update_historys->created_by   = Auth::id();
                        $update_historys->save();
                    } else {
                        $getFirstValues                 = Approve::where('ticket_id', '=', $inputData['id'])->where('action', '=', 0)->orderBy('id', 'asc')->first();
                        $update_nextUser                = Ticket::find($inputData['id']);
                        $update_nextUser->now_ticket_at = $inputData['forwardBy'];
                        $update_nextUser->tStatus       = 7;
                        $update_nextUser->save();
                        $update_historys               = new TicketHistory();
                        $update_historys->ticket_id    = $inputData['id'];
                        $update_historys->tDescription = $inputData['Commentbox'];
                        $update_historys->tStatus      = $formAction;
                        $update_historys->action_to    = Auth::id();
                        $update_historys->created_by   = Auth::id();
                        $update_historys->save();
                    }
                    $userInfoMail = User::find($inputData['forwardBy']);
                    if (!empty($userInfoMail->email) && !$this->vacationService->checkUserHasSetVacationOrNot($update_nextUser['now_ticket_at'])) {
                        $subMessage = 'You have a new request notification';
                        $this->mailSend($inputData, $userInfoMail, $update_nextUser, $subMessage);
                    }

                    $ticketData = Ticket::find($inputData['id']);
                    $log            = json_decode($ticketData->thistory, true);
                    $logInfo        = array();


                    foreach ($log as $key => $HistoryInfo) {

                        # For existing ticket
                        # Get user type id
                        if( isset($HistoryInfo['user_type']) && !empty($HistoryInfo['user_type']) ){
                            $existing_user_type_id = $this->UserTypeIdHistoryLog($HistoryInfo['user_type']);
                        }
                        else{
                            $existing_user_type_id = 101;
                        }
                        # Get user status id
                        if( isset($HistoryInfo['user_status']) && !empty($HistoryInfo['user_status']) ){
                            $existing_status_type_id = $this->UserStatusIdHistoryLog($HistoryInfo['user_status']);
                        }
                        else{
                            $existing_status_type_id = 102;
                        }


                        if ($HistoryInfo['user_id'] == Auth::id() && $HistoryInfo['user_action'] == 0) {

                            $logInfo[] = [
                                'user_id' => $HistoryInfo['user_id'],
                                'user_name' => $HistoryInfo['user_name'],
                                'user_type' => $HistoryInfo['user_type'],
                                'user_type_id' => !empty($HistoryInfo['user_type_id']) ? $HistoryInfo['user_type_id'] : $existing_user_type_id,
                                'user_status' => 'Appoved And Forward',
                                'user_status_id' => self::STATUS_APPROVE_FORWARD,
                                'user_action' => 1,
                                'date' => date('d-m-Y H:i:s')
                            ];
                            $userInfo  = User::where('id', $inputData['forwardBy'])->first();

                            $logInfo[] = [
                                'user_id' => $userInfo->id,
                                'user_name' => $userInfo->name,
                                'user_type' => 'Forward User',
                                'user_type_id' => self::TYPE_FORWARDED,
                                'user_status' => 'Pending',
                                'user_status_id' => self::STATUS_PENDING,
                                'user_action' => 0,
                                'date' => ''
                            ];

                        } else {
                            $logInfo[] = [
                                'user_id' => $HistoryInfo['user_id'],
                                'user_name' => $HistoryInfo['user_name'],
                                'user_type' => $HistoryInfo['user_type'],
                                'user_type_id' => !empty($HistoryInfo['user_type_id']) ? $HistoryInfo['user_type_id'] : $existing_user_type_id,
                                'user_status' => $HistoryInfo['user_status'],
                                'user_status_id' => !empty($HistoryInfo['user_status_id']) ? $HistoryInfo['user_status_id'] : $existing_status_type_id,
                                'user_action' => $HistoryInfo['user_action'],
                                'date' => isset($HistoryInfo['date']) && !empty($HistoryInfo['date']) ? $HistoryInfo['date'] : ''
                            ];
                        }

                    }

                    $ticketData->thistory = json_encode($logInfo);
                    $ticketData->save();

                    if ($this->vacationService->checkUserHasSetVacationOrNot($ticketData['now_ticket_at'])) {
                        $this->vacationService->forwardTicket($ticketData);
                    };
               });
            }

            // ====================Appoved And Acknowledgement=========================
            if ($formAction == '404') {
                $formAction = 9;

                DB::transaction(function () use ($inputData, $formAction) {
                    if (empty($inputData['AcknowledgementBy'])) {
                        return redirect('/request/inbox')->with('error', 'You did not select any user for acknowledgement');
                    }

                    $statusUpdate = Approve::where('ticket_id', '=', $inputData['id'])->where('user_id', '=', Auth::id())->update(['action' => 1]);
                    if ($statusUpdate) {
                        $getFirstValues = Approve::where('ticket_id', '=', $inputData['id'])->where('action', '=', 0)->orderBy('id', 'asc')->first();
                        if (!empty($getFirstValues)) {
                            $update_nextUser                = Ticket::find($inputData['id']);
                            $update_nextUser->now_ticket_at = $getFirstValues->user_id;
                            $update_nextUser->tStatus       = 9;
                            $update_nextUser->save();
                            $mailUser = $getFirstValues->user_id;
                        } else {
                            $update_nextUser                = Ticket::find($inputData['id']);
                            $update_nextUser->now_ticket_at = $update_nextUser->initiator_id;
                            $update_nextUser->tStatus       = 4;
                            $update_nextUser->save();
                            $mailUser = $update_nextUser->initiator_id;
                        }
                        $update_historys               = new TicketHistory();
                        $update_historys->ticket_id    = $inputData['id'];
                        $update_historys->tDescription = $inputData['Commentbox'];
                        $update_historys->tStatus      = 4;
                        $update_historys->action_to    = Auth::id();
                        $update_historys->created_by   = Auth::id();
                        $update_historys->save();
                        $update_notification_historys               = new TicketHistory();
                        $update_notification_historys->ticket_id    = $inputData['id'];
                        $update_notification_historys->tDescription = 'Notification';
                        $update_notification_historys->tStatus      = $formAction;
                        $update_notification_historys->action_to    = $inputData['AcknowledgementBy'];
                        $update_notification_historys->created_by   = Auth::id();
                        $update_notification_historys->save();
                    } else {
                        $getFirstValues = Approve::where('ticket_id', '=', $inputData['id'])->where('action', '=', 0)->orderBy('id', 'asc')->first();
                        if (!empty($getFirstValues)) {
                            $update_nextUser                = Ticket::find($inputData['id']);
                            $update_nextUser->now_ticket_at = $getFirstValues->user_id;
                            $update_nextUser->tStatus       = 9;
                            $update_nextUser->save();
                            $mailUser = $getFirstValues->user_id;
                        } else {
                            $update_nextUser                = Ticket::find($inputData['id']);
                            $update_nextUser->now_ticket_at = $update_nextUser->initiator_id;
                            $update_nextUser->tStatus       = 4;
                            $update_nextUser->save();
                            $mailUser = $update_nextUser->initiator_id;

                        }
                        $update_historys               = new TicketHistory();
                        $update_historys->ticket_id    = $inputData['id'];
                        $update_historys->tDescription = $inputData['Commentbox'];
                        $update_historys->tStatus      = 4;
                        $update_historys->action_to    = Auth::id();
                        $update_historys->created_by   = Auth::id();
                        $update_historys->save();
                        $update_notification_historys               = new TicketHistory();
                        $update_notification_historys->ticket_id    = $inputData['id'];
                        $update_notification_historys->tDescription = 'Notification';
                        $update_notification_historys->tStatus      = $formAction;
                        $update_notification_historys->action_to    = $inputData['AcknowledgementBy'];
                        $update_notification_historys->created_by   = Auth::id();
                        $update_notification_historys->save();
                    }
                    $userInfoMail = User::find($mailUser);
                    if (!empty($userInfoMail->email)) {
                        $subMessage = 'You have a new request notification';
                        $this->mailSend($inputData, $userInfoMail, $update_nextUser, $subMessage);
                    }

                    $udateTicketLog = Ticket::find($inputData['id']);
                    $log            = json_decode($udateTicketLog->thistory, true);
                    $logInfo        = array();
                    foreach ($log as $key => $HistoryInfo) {

                        # For existing ticket
                        # Get user type id
                        if( isset($HistoryInfo['user_type']) && !empty($HistoryInfo['user_type']) ){
                            $existing_user_type_id = $this->UserTypeIdHistoryLog($HistoryInfo['user_type']);
                        }
                        else{
                            $existing_user_type_id = 101;
                        }
                        # Get user status id
                        if( isset($HistoryInfo['user_status']) && !empty($HistoryInfo['user_status']) ){
                            $existing_status_type_id = $this->UserStatusIdHistoryLog($HistoryInfo['user_status']);
                        }
                        else{
                            $existing_status_type_id = 102;
                        }


                        if ($HistoryInfo['user_id'] == Auth::id() && $HistoryInfo['user_action'] == 0) {

                            $logInfo[] = [
                                'user_id' => $HistoryInfo['user_id'],
                                'user_name' => $HistoryInfo['user_name'],
                                'user_type' => $HistoryInfo['user_type'],
                                'user_type_id' => !empty($HistoryInfo['user_type_id']) ? $HistoryInfo['user_type_id'] : $existing_user_type_id,
                                'user_status' => 'Appoved And Acknowledgement',
                                'user_status_id' => self::STATUS_APPROVE_ACKNOW,
                                'user_action' => 1,
                                'date' => date('d-m-Y H:i:s')
                            ];

                            $userInfo  = User::where('id', $inputData['AcknowledgementBy'])->first();

                            $logInfo[] = [
                                'user_id' => $userInfo->id,
                                'user_name' => $userInfo->name,
                                'user_type' => 'Acknowledgement User',
                                'user_type_id' => self::TYPE_ACKNOWLEDGE,
                                'user_status' => 'Acknowledged',
                                'user_status_id' => self::STATUS_ACKNOWLEDGED,
                                'user_action' => 0,
                                'date' => date('d-m-Y H:i:s')
                            ];

                        } else {

                            $logInfo[] = [
                                'user_id' => $HistoryInfo['user_id'],
                                'user_name' => $HistoryInfo['user_name'],
                                'user_type' => $HistoryInfo['user_type'],
                                'user_type_id' => !empty($HistoryInfo['user_type_id']) ? $HistoryInfo['user_type_id'] : $existing_user_type_id,
                                'user_status' => $HistoryInfo['user_status'],
                                'user_status_id' => !empty($HistoryInfo['user_status_id']) ? $HistoryInfo['user_status_id'] : $existing_status_type_id,
                                'user_action' => $HistoryInfo['user_action'],
                                'date' => isset($HistoryInfo['date']) && !empty($HistoryInfo['date']) ? $HistoryInfo['date'] : ''
                            ];
                        }

                    }

                    $udateTicketLog->thistory = json_encode($logInfo);
                    $udateTicketLog->save();
                });
            }

        } else {
            echo "select Action";
        }

        $TicketID = $inputData['id'];
        if ($request->hasfile('tFile')) {
            $fileData = array();
            foreach ($request->file('tFile') as $file) {
                $fileName        = Auth::id() . '-' . time() . '-' . $file->getClientOriginalName();
                $fileType        = $file->getClientOriginalExtension();
                $destinationPath = public_path('/upload/ticket_file/' . date('Y'));
                $file->move($destinationPath, $fileName);
                $folder     = 'upload/ticket_file/' . date('Y');
                $fileData[] = ['ticket_id' => $TicketID, 'file_name' => $fileName, 'file_type' => $fileType, 'folder' => $folder];

            }
            DB::table('tickets_files')->insert($fileData);
        }

        $ticketStatus = $this->ticketUpdateStatus($request->formAction);
        $msg = !is_null($ticketStatus) ? "as $ticketStatus" : "";
        $this->logStore('updated','ticket',"$ticket_info->tSubject( $ticket_info->tReference_no ) ticket successfully updated $msg.","request inbox");

        $redirect_msg = 'Successfully Updated';
        return redirect('request/inbox')->with('status', $redirect_msg);

    } // RequestStatusUpdate() End

    public function ticketUpdateStatus($type = null)
    {
        $status = [
            "4" => "approved",
            "5" => "rejected",
            "6" => "request for info",
            "7" => "forwarded",
            "504" => "approved and forwarded",
            "404" => "approved and acknowledgement",
        ];

        return $status[$type] ?? null;
    }

    public function getStatusList()
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

    public function DraftEdit(Request $request, $id = 0)
    {

        $previousUrl = URL::previous();
        $TicketInfo  = Ticket::find($id);

        # Check drafts details page not show to other except initiator
        if( $TicketInfo->tStatus == 1 && $TicketInfo->initiator_id != Auth::id() ){
        	return redirect('/request/drafts')->with('error', 'You are not authorised to see this ticket!');
        }

        $subcatList  = SubCategory::where('cat_id', '=', $TicketInfo->cat_id)->where('active_date', '<=', date('Y-m-d'))
            ->Where(function ($query) {
                $query->whereNull('deactive_date');
                $query->orWhere('deactive_date', '>=', date('Y-m-d'));
            })->get();
        $recommenderList = User::select('users.*')
            ->join('ticket_approve as TA', 'TA.user_id', '=', 'users.id')
            ->where('TA.user_type', '=', 1)
            ->where('TA.ticket_id', '=', $id)
            ->get();
        // print_r($recommenderList);

        // exit();
        $approverList = User::select('users.*')
            ->join('ticket_approve as TA', 'TA.user_id', '=', 'users.id')
            ->where('TA.user_type', '=', 2)
            ->where('TA.ticket_id', '=', $id)
            ->get();
        // print_r($recommenderList);

        $PreviousComment = DB::table('ticket_historys as TH')
            ->join('users as UI', 'TH.action_to', '=', 'UI.id')
            ->select('TH.*', 'UI.name as User_name')
            ->where('TH.ticket_id', '=', $id)
            ->orderBy('TH.id', 'asc')
            ->get();

        // exit();
        $tStatus = $TicketInfo->tStatus;
        if ($tStatus == 2) {
            $pageTitle = 'Pending Requests';
            $mthdName  = 'pending';

        } elseif ($tStatus == 1) {
            $pageTitle = 'Draft Requests';
            $mthdName  = 'draft';

        } elseif ($tStatus == 6) {
            $pageTitle = 'Requests for Information';
            $mthdName  = 'request_info';

        } elseif ($tStatus == 11) {
            $pageTitle = 'Requests for Information';
            $mthdName  = 'request_info';

        } elseif ($tStatus == 5) {
            $pageTitle = 'Rejected Requests';
            $mthdName  = 'rejected';

        } else {
            $pageTitle = 'Approved Requests';
            $mthdName  = 'approved';
        }

        $attachmentFile = DB::table('tickets_files')->where('ticket_id', '=', $id)->get();

        $data = [
            'pageTitle'       => 'Details View Request',
            'catList'         => Category::where('active_date', '<=', date('Y-m-d'))
                ->Where(function ($query) {
                    $query->whereNull('deactive_date');
                    $query->orWhere('deactive_date', '>=', date('Y-m-d'));
                })->orderBy('id', 'DESC')->pluck('name', 'id'),
            'userList'        => User::pluck('users.name', 'users.id'),
            'ctrlName'        => 'ticket',
            'mthdName'        => $mthdName,
            'TicketInfo'      => $TicketInfo,
            'subcatList'      => $subcatList,
            'previousUrl'     => $previousUrl,
            'approverList'    => $approverList,
            'recommenderList' => $recommenderList,
            'attachmentFile'  => $attachmentFile,
            'PreviousComment' => $PreviousComment,
            'CompanyName'     => Company::where('active_date', '<=', date('Y-m-d'))
                ->Where(function ($query) {
                    $query->whereNull('deactive_date');
                    $query->orWhere('deactive_date', '>=', date('Y-m-d'));
                })->pluck('name', 'id'),
            'userList2'       => User::get(),
        ];

        return view('ticket.edit_draft_request', compact('data'));

    }

    public function DraftDelete(Request $request, $id)
    {
        try {
            $id = Crypt::decryptString($id);

            $findTicket = Ticket::findOrFail($id);
            if (!empty($findTicket)) {
                $TicketID = $findTicket->id;

                $approverData = DB::table('ticket_approve')->where('ticket_id', $TicketID)->delete();
                $FileData     = DB::table('tickets_files')->where('ticket_id', $TicketID)->delete();
                $HistorysData = DB::table('ticket_historys')->where('ticket_id', $TicketID)->delete();
                $TicketData   = Ticket::where('id', $TicketID)->delete();

            } else {
                $redirect_msg = 'Something wrong try later';
                return Redirect::back()->with('error', $redirect_msg);
            }
            $redirect_msg = 'Successfully Deleted';
            return Redirect::back()->with('status', $redirect_msg);
        } catch (Exception $e) {
            return back()->withError($e->getCode() . ' : ' . $e->getMessage())->withInput();
        }

    }
    public function deleteOldFile(Request $request)
    {
        $inputData = $request->all();
        $id        = $inputData['FileId'];
        $update    = DB::table('tickets_files')->where('id', $id)->update(['is_delete' => 1]);
        $result    = array();
        if ($update) {

            $result = ['Result' => '200', 'ms' => 'Delete Done', 'id' => $id];
        } else {
            $result = ['Result' => '100', 'ms' => 'Delete Done', 'id' => $id];
        }
        return response($result);

    }

    public function UpdateDraftRequest(Request $request)
    {
        $inputData = $request->all();
        // echo "<pre>";
        // print_r($inputData);
        $this->validate($request, [
            'cat_id'       => 'required|max:10',
            'sub_cat_id'   => 'required',
            'tSubject'     => 'required',
            'tDescription' => 'required',

        ]);

        if (empty($inputData['recommender_id']) or empty($inputData['approver_id'])) {
            $redirect_msg = 'Mandatory Field is Missing ';

            return redirect('/request/new')->with('error', $redirect_msg);

        }
        if (!empty($inputData['recommender_id'][0])) {
            $ticNow = $inputData['recommender_id'][0];
        } else {
            if (!isset($inputData['recommender_id'][1])) {
                $redirect_msg = 'Mandatory Field is Missing ';
                return redirect('/request/new')->withInput(Input::all())->with('error', $redirect_msg);
            }
            $ticNow = $inputData['recommender_id'][1];

        }

        $ticketRequest                = Ticket::find($inputData['id']);
        $ticketRequest->tReference_no = null;
        $ticketRequest->company_id    = $inputData['company_id'];
        $ticketRequest->cat_id        = $inputData['cat_id'];
        $ticketRequest->sub_cat_id    = $inputData['sub_cat_id'];
        $ticketRequest->initiator_id  = Auth::id(); //$request->session()->get('userID')
        $ticketRequest->tSubject      = $inputData['tSubject'];
        $ticketRequest->tDescription  = $inputData['tDescription'];
        $ticketRequest->tStatus       = $inputData['tStatus'];
        $ticketRequest->now_ticket_at = $ticNow;
        $ticketRequest->save();
        $TicketID = $ticketRequest->id; // $TicketID=2;

        # update reference number
        if ($request['tStatus'] == 2) {
            $companyInfo     = Company::where('id', '=', $inputData['company_id'])->select('short_name')->first();
            $categoryInfo    = Category::where('id', '=', $inputData['cat_id'])->select('name')->first();
            $subCategoryInfo = SubCategory::where('id', '=', $inputData['sub_cat_id'])->select('name')->first();
            // $requestId       = Ticket::whereMonth('created_at', date('m'))->count();
            // $requestId       = $requestId + 1;

            $referenceNo  = $companyInfo->short_name . '-' . $categoryInfo->name . '-' . $subCategoryInfo->name . '-' . date('mY') . $TicketID;
            # update reference
            Ticket::where('id', $TicketID)->update(['tReference_no' => $referenceNo]);

            $redirect_to     = '/request/pending';
            $redirect_msg    = 'Successfully new request submitted!';
        } else {
            $referenceNo  = null;
            $redirect_to  = '/request/drafts';
            $redirect_msg = 'Request successfully saved as draft.';
        }



        if ($request->hasfile('tFile')) {
            $fileData = array();

            foreach ($request->file('tFile') as $file) {

                $fileName = Auth::id() . '-' . time() . '-' . $file->getClientOriginalName();
                $fileType = $file->getClientOriginalExtension();
                $destinationPath = public_path('/upload/ticket_file/' . date('Y'));
                $file->move($destinationPath, $fileName);
                $folder     = 'upload/ticket_file/' . date('Y');
                $fileData[] = ['ticket_id' => $TicketID, 'file_name' => $fileName, 'file_type' => $fileType, 'folder' => $folder];

            }

            DB::table('tickets_files')->insert($fileData);
        }

        DB::table('ticket_approve')->where('ticket_id', $TicketID)->where('user_type', 1)->delete();
        $recommender_id = $inputData['recommender_id'];
        if (!empty($recommender_id)) {
            $recommenderInfo = array();
            foreach ($recommender_id as $key => $file) {
                $recommenderID     = $recommender_id[$key];
                $recommenderInfo[] = ['ticket_id' => $TicketID, 'user_id' => $recommenderID, 'user_type' => 1];

            }
            DB::table('ticket_approve')->insert($recommenderInfo);
        }
        DB::table('ticket_approve')->where('ticket_id', $TicketID)->where('user_type', 2)->delete();
        $approver_id = $inputData['approver_id'];
        if (!empty($approver_id)) {
            $approverInfo = array();
            foreach ($approver_id as $key => $file) {
                $approverID     = $approver_id[$key];
                $approverInfo[] = ['ticket_id' => $TicketID, 'user_id' => $approverID, 'user_type' => 2];
            }
            DB::table('ticket_approve')->insert($approverInfo);
        }



        # Send mail
        # save as Drafts tStatus 1
        if($request['tStatus'] != 1){
	        $userInfoMail = User::find($ticNow);
	        if (!empty($userInfoMail->email)) {

	            $id  = $TicketID;
	            $url = url('request/details/').'/' . $id;

	            // $maildata=['URL'=>$url];

	            $maildata = array(
	                'URL'           => $url,
	                'name'          => !empty($userInfoMail->name) ? $userInfoMail->name : ' ',
	                'onlySubject'   => !empty($inputData['tSubject']) ? $inputData['tSubject'] : ' ',
	                'tReference_no' => !empty($referenceNo) ? $referenceNo : ' ',
	            );

	            try {
	                Mail::send(['html' => 'emails.mail'], $maildata, function ($message) use ($userInfoMail) {
	                    if (empty($userInfoMail->name)) {
	                        $userInfoMailName = '';
	                    } else {
	                        $userInfoMailName = $userInfoMail->name;
	                    }

	                    $message->to("$userInfoMail->email", "$userInfoMailName")->subject('You have a new request notification');
	                    $message->from('ams.noreply@psgbd.com', 'AMS Notification System');
	                });
	            } catch (Exception $e) {
	                #continue
	            }

	        }
        }

        $status = $request->tStatus == 1 ? "draft" : "initiated";
        $this->logStore('updated','ticket',"$ticketRequest->tSubject( $ticketRequest->tReference_no ) ticket successfully $status.",'requests drafts');

        return redirect($redirect_to)->with('status', $redirect_msg);

    }

    public function searchAdUser(Request $request)
    {
        $input  = $request->all();
        $search = $input['term'];
        $users  = DB::table('users')
        // ->select('name','id')
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('telephonenumber', 'like', "%{$search}%");
            })
            ->where('is_active', '=' , 1)
            ->take(10)
            ->get();
        return json_encode($users);
    }
    public function searchAdUserInModal(Request $request)
    {
        $input  = $request->all();
        $search = $input['searchInput'];
        $users  = DB::table('users')
        // ->select('name','id')
            ->where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhere('title', 'like', "%{$search}%")
            ->orWhere('department', 'like', "%{$search}%")
            ->orWhere('telephonenumber', 'like', "%{$search}%")
            ->where('is_active', '=' , 1)
        // ->take(10)
            ->get();
        return json_encode($users);
    }

    public function advancedSearchAdUserInModal(Request $request)
    {
        $inputData = $request->all();
        // print_r($inputData);

        if (!empty($inputData['email'])) {
            $whereData = $inputData['email'];
        } elseif (!empty($inputData['email'])) {
            $whereData = $inputData['email'];
        } elseif (!empty($inputData['email'])) {
            $whereData = $inputData['email'];
        } elseif (!empty($inputData['title'])) {
            $whereData = $inputData['title'];
        } elseif (!empty($inputData['department'])) {
            $whereData = $inputData['department'];
        } elseif (!empty($inputData['company'])) {
            $whereData = $inputData['company'];
        } elseif (!empty($inputData['phone'])) {
            $whereData = $inputData['phone'];
        }

        $title      = $inputData['title'];
        $department = $inputData['department'];
        $phone      = $inputData['phone'];
        $email      = $inputData['email'];
        $user_info  = DB::table('users');

        if (!empty($inputData['email'])) {
            $user_info->Where('email', 'like', "%{$email}%");
        }
        if (!empty($inputData['title'])) {
            $user_info->Where('title', 'like', "%{$title}%");
        }
        if (!empty($inputData['department'])) {
            $user_info->Where('department', 'like', "%{$department}%");
        }
        if (!empty($inputData['phone'])) {
            $user_info->Where('telephonenumber', 'like', "%{$phone}%");
        }
        // $user_info->take(10)->get();
        $result = $user_info->get();
        // print_r($result);

        if (!empty($result)) {
            return json_encode($result);

        } else {
            $habib = array();
            return json_encode($habib);
        }

    }

    public function reassign(Request $request)
    {
        $data = [
            'pageTitle' => 'Reassign Request',
            // 'userType'  => Role::orderBy('id', 'DESC')->pluck('name', 'id'),
            'ctrlName'  => 'user',
            'mthdName'  => 'reassign',
        ];
        $pageName='reassign.create';
        return Helper::checkAdmin($pageName,$data);
    }
    public function searchAssignment(Request $request)
    {
        $userId = $request->input('userId');

        $Initiat = Ticket::where('initiator_id', $userId)->whereNotIn('tStatus', [4, 1, 5, 10])->select('id', 'tReference_no', 'tStatus', 'tSubject')->get();
        $pending = Ticket::where('now_ticket_at', $userId)->whereNotIn('tStatus', [4, 1, 5, 10])->select('id', 'tReference_no', 'tStatus', 'tSubject')->get();
        $circle  = DB::table('ticket_approve as TA')
            ->join('tickets', 'TA.ticket_id', '=', 'tickets.id')
            ->where('TA.user_id', '=', $userId)
            ->where('TA.action', '!=', 1)
        // ->OrWhere('tickets.now_ticket_at','=',$userId)
            ->select('tickets.id', 'tickets.tReference_no', 'tickets.tStatus', 'tickets.tSubject')
            ->get();

        $getStatusList = self::getStatusList();
        $data          = ['Initiat' => $Initiat, 'circle' => $circle, 'pending' => $pending];
        $pageName='reassign.view_result';
        return Helper::checkAdmin($pageName,$data)->with(['statusResult' => $getStatusList]);
    }

    public function updateAssignment(Request $request)
    {
        $userId      = $request->input('user');
        $ticket_id   = $request->input('request_id');
        $requestType = $request->input('requestType');
        $ipAddress   = $request->getClientIp();
        $info        = Ticket::find($ticket_id);
        if (empty($userId)) {
            return '400';
        }

        if ($requestType == 'Initiat') {

            $info->initiator_id = $userId;
            $insertValu = ['user_id' => Auth::id(), 'ip_address' => $ipAddress, 'description' => "Request Assignment Change By Admin MR. ". Auth::user()->name ." and Email ".Auth::user()->email ." there is Orginal User $info->initiator_id and Replace user $userId "];

        } elseif ($requestType == 'Pending') {
            $statusUpdate = Approve::where('ticket_id', '=', $ticket_id)->where('user_id', '=', $info->now_ticket_at)->update(['user_id' => $userId]);

            $log     = json_decode($info->thistory, true);
            $logInfo = array();
            foreach ($log as $key => $HistoryInfo) {
                if ($HistoryInfo['user_id'] == $info->now_ticket_at && $HistoryInfo['user_action'] == 0) {
                    $logInfo[] = ['user_id' => $HistoryInfo['user_id'], 'user_name' => $HistoryInfo['user_name'], 'user_type' => $HistoryInfo['user_type'], 'user_status' => 'Admin Forward', 'user_action' => 1];
                    $userInfo  = User::where('id', $userId)->first();
                    $logInfo[] = ['user_id' => $userInfo->id,
                        'user_name' => $userInfo->name,
                        'user_type' => 'Forward',
                        'user_status' => 'Assign User',
                        'user_action' => 0,
                        'date' => date('d-m-Y H:i:s')];
                } else {
                    $logInfo[] = ['user_id' => $HistoryInfo['user_id'],
                        'user_name' => $HistoryInfo['user_name'],
                        'user_type' => $HistoryInfo['user_type'],
                        'user_status' => $HistoryInfo['user_status'],
                        'user_action' => $HistoryInfo['user_action'],
                        'date' => isset($HistoryInfo['date']) && !empty($HistoryInfo['date']) ? $HistoryInfo['date'] : ''];
                }

            }
            $info->now_ticket_at = $userId;
            $info->tStatus       = 7;
            $insertValu          = ['user_id' => Auth::id(), 'ip_address' => $ipAddress, 'description' => "Request Assignment Change By Admin MR. ". Auth::user()->name ." and Email ".Auth::user()->email ." there is Orginal User $info->initiator_id and Replace user $userId "];
            $info->thistory      = json_encode($logInfo);

        }

        if ($info->save()) {
            $activity     = DB::table('activity_logs')->insert($insertValu);

            $this->logStore('updated','ticket',"$info->tSubject( $info->tReference_no ) ticket updated.","manage reassign");

            $returnResult = '100';
        } else {
            $returnResult = '400';
        }

        return $returnResult;

    }

    public function sendMail($TicketID, $Subject, $ticNow)
    {
        $ticNow   = $ticNow;
        $TicketID = $TicketID;
        $SubjInfo = Ticket::find($TicketID);
        $Subject  = $SubjInfo->tSubject . ' From ' . Auth::user()->name;
        $id       = $TicketID;

        $userInfoMail = User::find($ticNow);
        if (!empty($userInfoMail->email)) {

            $url      = url('request/details/').'/' . $id;
            $maildata = ['URL' => $url, 'name' => $userInfoMail->name, 'subject' => $Subject, 'tReference_no' => $SubjInfo->tReference_no, 'onlySubject' => $SubjInfo->tSubject];
            Mail::send(['html' => 'emails.mail'], $maildata, function ($message) use ($userInfoMail, $Subject) {
                if (empty($userInfoMail->name)) {
                    $userInfoMailName = '';
                } else {
                    $userInfoMailName = $userInfoMail->name;
                }
                $message->to($userInfoMail->email, $userInfoMailName)->subject($Subject);
                $message->from('ams.noreply@psgbd.com', 'AMS Notification System');
            });

        }

    }

    public function sendApproedMail($TicketID, $Subject, $ticNow)
    {
        $ticNow   = $ticNow;
        $TicketID = $TicketID;
        $SubjInfo = Ticket::find($TicketID);
        $Subject  = $SubjInfo->tSubject;
        $id       = $TicketID;

        $userInfoMail = User::find($ticNow);
        if (!empty($userInfoMail->email)) {

            $url      = url('request/details/').'/' . $id;
            $maildata = ['URL' => $url, 'name' => $userInfoMail->name, 'subject' => $Subject];
            Mail::send(['html' => 'emails.armail'], $maildata, function ($message) use ($userInfoMail, $Subject) {
                if (empty($userInfoMail->name)) {
                    $userInfoMailName = '';
                } else {
                    $userInfoMailName = $userInfoMail->name;
                }
                $message->to($userInfoMail->email, $userInfoMailName)->subject($Subject);
                $message->from('ams.noreply@psgbd.com', 'AMS Notification System');
            });

        }

    }

    public function sendRejectedMail($TicketID, $Subject, $ticNow)
    {
        $ticNow   = $ticNow;
        $TicketID = $TicketID;
        $SubjInfo = Ticket::find($TicketID);
        $Subject  = $SubjInfo->tSubject;
        $id       = $TicketID;

        $userInfoMail = User::find($ticNow);
        if (!empty($userInfoMail->email)) {

            $url      = url('request/details/').'/' . $id;
            $maildata = ['URL' => $url, 'name' => $userInfoMail->name, 'subject' => $Subject];
            Mail::send(['html' => 'emails.rejectMail'], $maildata, function ($message) use ($userInfoMail, $Subject) {
                if (empty($userInfoMail->name)) {
                    $userInfoMailName = '';
                } else {
                    $userInfoMailName = $userInfoMail->name;
                }
                $message->to($userInfoMail->email, $userInfoMailName)->subject($Subject);
                $message->from('ams.noreply@psgbd.com', 'AMS Notification System');
            });

        }

    }

    public function fileUploadTinyMce(Request $request)
    {
        $file            = $request->file('file');
        $fileName        = Auth::id() . '-' . time() . '-' . $file->getClientOriginalName();
        $fileType        = $file->getClientOriginalExtension();
        $destinationPath = public_path('/upload/ticket_file/' . date('Y') . '/tinymce');
        $file->move($destinationPath, $fileName);
        // $folder = 'public/upload/ticket_file/tinymce/'.date('Y').'/'.$fileName;
        $folder = URL::to('/upload/ticket_file/' . date('Y') . '/tinymce/' . $fileName);

        return response()->json(['location' => $folder]);

    }


    public static function force_balance_tags( $text ) {
		$tagstack = array();
		$stacksize = 0;
		$tagqueue = '';
		$newtext = '';
		// Known single-entity/self-closing tags
		$single_tags = array( 'area', 'base', 'basefont', 'br', 'col', 'command', 'embed', 'frame', 'hr', 'img', 'input', 'isindex', 'link', 'meta', 'param', 'source' );
		// Tags that can be immediately nested within themselves
		$nestable_tags = array( 'blockquote', 'div', 'object', 'q', 'span' );

		// WP bug fix for comments - in case you REALLY meant to type '< !--'
		$text = str_replace('< !--', '<    !--', $text);
		// WP bug fix for LOVE <3 (and other situations with '<' before a number)
		$text = preg_replace('#<([0-9]{1})#', '&lt;$1', $text);

		while ( preg_match("/<(\/?[\w:]*)\s*([^>]*)>/", $text, $regex) ) {
			$newtext .= $tagqueue;

			$i = strpos($text, $regex[0]);
			$l = strlen($regex[0]);

			// clear the shifter
			$tagqueue = '';
			// Pop or Push
			if ( isset($regex[1][0]) && '/' == $regex[1][0] ) { // End Tag
				$tag = strtolower(substr($regex[1],1));
				// if too many closing tags
				if ( $stacksize <= 0 ) {
					$tag = '';
					// or close to be safe $tag = '/' . $tag;
				}
				// if stacktop value = tag close value then pop
				elseif ( $tagstack[$stacksize - 1] == $tag ) { // found closing tag
					$tag = '</' . $tag . '>'; // Close Tag
					// Pop
					array_pop( $tagstack );
					$stacksize--;
				} else { // closing tag not at top, search for it
					for ( $j = $stacksize-1; $j >= 0; $j-- ) {
						if ( $tagstack[$j] == $tag ) {
						// add tag to tagqueue
							for ( $k = $stacksize-1; $k >= $j; $k--) {
								$tagqueue .= '</' . array_pop( $tagstack ) . '>';
								$stacksize--;
							}
							break;
						}
					}
					$tag = '';
				}
			} else { // Begin Tag
				$tag = strtolower($regex[1]);

				// Tag Cleaning

				// If it's an empty tag "< >", do nothing
				if ( '' == $tag ) {
					// do nothing
				}
				// ElseIf it presents itself as a self-closing tag...
				elseif ( substr( $regex[2], -1 ) == '/' ) {
					// ...but it isn't a known single-entity self-closing tag, then don't let it be treated as such and
					// immediately close it with a closing tag (the tag will encapsulate no text as a result)
					if ( ! in_array( $tag, $single_tags ) )
						$regex[2] = trim( substr( $regex[2], 0, -1 ) ) . "></$tag";
				}
				// ElseIf it's a known single-entity tag but it doesn't close itself, do so
				elseif ( in_array($tag, $single_tags) ) {
					$regex[2] .= '/';
				}
				// Else it's not a single-entity tag
				else {
					// If the top of the stack is the same as the tag we want to push, close previous tag
					if ( $stacksize > 0 && !in_array($tag, $nestable_tags) && $tagstack[$stacksize - 1] == $tag ) {
						$tagqueue = '</' . array_pop( $tagstack ) . '>';
						$stacksize--;
					}
					$stacksize = array_push( $tagstack, $tag );
				}

				// Attributes
				$attributes = $regex[2];
				if ( ! empty( $attributes ) && $attributes[0] != '>' )
					$attributes = ' ' . $attributes;

				$tag = '<' . $tag . $attributes . '>';
				//If already queuing a close tag, then put this tag on, too
				if ( !empty($tagqueue) ) {
					$tagqueue .= $tag;
					$tag = '';
				}
			}
			$newtext .= substr($text, 0, $i) . $tag;
			$text = substr($text, $i + $l);
		}

		// Clear Tag Queue
		$newtext .= $tagqueue;

		// Add Remaining text
		$newtext .= $text;

		// Empty Stack
		while( $x = array_pop($tagstack) )
			$newtext .= '</' . $x . '>'; // Add remaining tags to close

		// WP fix for the bug with HTML comments
		$newtext = str_replace("< !--","<!--",$newtext);
		$newtext = str_replace("<    !--","< !--",$newtext);

		return $newtext;
	}
} // class end
