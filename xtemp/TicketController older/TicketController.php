<?php

namespace App\Http\Controllers;

use App\Ticket;
use App\TicketHistory;
use App\Category;
use App\SubCategory;
use App\TicketApprove as Approve;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTicket;
use Exception;
use Auth;
use DB;
use URL;
use App\Company;
use Illuminate\Support\Facades\Input;
Use Redirect;
use Mail;
use Illuminate\Support\Facades\Crypt;

    class TicketController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->middleware('auth');
        // $this->middleware('role:Initiator', ['only' => ['create', 'store', 'ticket_list']]);
    }
 public function circle_test(){

     $userInfo=User::where('id',759)->first();
                // $logInfo[]=[
                // 'user_id'=>$userInfo->id,
                // 'user_name'=>$userInfo->name,
                // 'user_type'=>'Forward',
                // 'user_status'=>'Forward User',
                // 'user_action'=>0,
                // 'date'=>''
                // ];
     print_r($userInfo);
     echo $userInfo['company_name'];
     echo "<br>";

                 $logInfo[]=[
                'user_id'=>$userInfo->id,
                'user_name'=>$userInfo->name,
                'designation'=>(!empty($userInfo->title)) ? $userInfo->title:'Designation Empty',
                'user_type'=>'Forwarded User',
                'department'=>(!empty($userInfo->department)) ? $userInfo->department:'Department Empty',
                'company'=>(!empty($userInfo->company_name)) ? $userInfo->company_name:'Company Empty',
                'user_status'=>'Pending',
                'user_action'=>0,
                'date'=>''
                ];
                print_r($logInfo);

 }
    /**
     * Create a new request.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'pageTitle' => 'New Request',
            'CompanyName'=>Company::where('active_date','<=',date('Y-m-d'))
                ->Where(function ($query) {
                $query->whereNull('deactive_date');
                $query->orWhere('deactive_date', '>=', date('Y-m-d'));
                })->orderBy('name')->pluck('name', 'id'),
            'catList'   => Category::where('active_date','<=',date('Y-m-d'))
                ->Where(function ($query) {
                $query->whereNull('deactive_date');
                $query->orWhere('deactive_date', '>=', date('Y-m-d'));
                })->orderBy('name')->pluck('name', 'id'),
            'userList'  => User::pluck('users.name', 'users.id'),
            'ctrlName'  => 'ticket',
            'mthdName'  => 'new',
            'userList2'=>User::get()
        ];
        return view('ticket.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTicket $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
// try{
   $validatedData = $request->validate([
        'company_id' => 'required|max:10',
        'cat_id' => 'required|max:10',
        'sub_cat_id' => 'required',
        'tSubject' => 'required',
        'tDescription' => 'required',
       
        ]);
        $inputData=$request->all();
        // echo "<pre>";
        // print_r($inputData);

        // exit();
$redirect_msg_wrong='';
        $fileSize=0;
         if($request->hasfile('tFile')){
    foreach($request->file('tFile') as $file){

        $fileSize+=$file->getClientSize();
            }
 $totalFilesize=number_format($fileSize / 1048576,2);
 if($totalFilesize>10.00){
    $redirect_msg = 'if user try to add more than 10 MB file “You have exceed your limit”';
 return redirect('/request/new')->withInput(Input::all())->with('error', $redirect_msg);
 }

}
            // echo $fileSize;
// exit();
      // $request->file('file')->getClientSize();

        if($request['tStatus'] == 2){
            $companyInfo=Company::where('id','=',$inputData['company_id'])->select('short_name')->first();
            $categoryInfo=Category::where('id','=',$inputData['cat_id'])->select('name')->first();
            $subCategoryInfo=SubCategory::where('id','=',$inputData['sub_cat_id'])->select('name')->first();
            $requestId=Ticket::whereMonth('created_at', date('m'))->count();
            $requestId=$requestId+1;
           
          $referenceNo  = $companyInfo->short_name.'-'.$categoryInfo->name.'-'.$subCategoryInfo->name.'-'.date('Ym').$requestId;//uniqid('REF_');
            $redirect_to  = '/request/pending';
            $redirect_msg = 'Successfully new request submitted!';
        }else{
            $referenceNo = NULL;
            $redirect_to  = '/request/drafts';
            $redirect_msg = 'Request successfully saved as draft.';
        }   

        if(empty($inputData['recommender_id']) Or empty($inputData['approver_id'])){
            $redirect_msg = 'Mandatory Field is Missing ';

             return redirect('/request/new')->with('error', $redirect_msg);

        }
        if(!empty($inputData['recommender_id'][0])){
            $ticNow=$inputData['recommender_id'][0];
        }else{
                 if(!isset($inputData['recommender_id'][1])){
                     $redirect_msg = 'Mandatory Field is Missing ';
 return redirect('/request/new')->withInput(Input::all())->with('error', $redirect_msg);
        }
           $ticNow=$inputData['recommender_id'][1]; 


        }
        $ticketRequest=new Ticket();
        $ticketRequest->tReference_no=$referenceNo;
        $ticketRequest->cat_id=$inputData['cat_id'];
        $ticketRequest->sub_cat_id=$inputData['sub_cat_id'];
        $ticketRequest->initiator_id=Auth::id();//$request->session()->get('userID')        
        $ticketRequest->tSubject=$inputData['tSubject'];
        $ticketRequest->tDescription=$inputData['tDescription'];
        $ticketRequest->tStatus=$inputData['tStatus'];
        $ticketRequest->now_ticket_at=$ticNow;
        $ticketRequest->priority=$inputData['priority'];
        $ticketRequest->company_id=$inputData['company_id'];
        // $ticketRequest->updated_at='0000-00-00 00:00:00';
        $ticketRequest->save();
       $TicketID=$ticketRequest->id; 
       $Subject='You have a new request notification'; 
        self::sendMail($TicketID,$Subject,$ticNow);

            // $TicketID=2;
         if($request->hasfile('tFile')){
            $fileData=array();

    foreach($request->file('tFile') as $file){
        $fileName  = Auth::id().'-'.time().'-'.$file->getClientOriginalName();
        $fileType=$file->getClientOriginalExtension();
        $destinationPath = public_path('/upload/ticket_file/'.date('Y'));
        $file->move($destinationPath, $fileName);  
        $folder='upload/ticket_file/'.date('Y');
        $fileData[]=['ticket_id'=>$TicketID,'file_name'=>$fileName,'file_type'=>$fileType,'folder'=>$folder];
            }
        DB::table('tickets_files')->insert($fileData);
         }    
        $thistory=array();
    $thistory[]=[
                'user_id'=>Auth::id(),
                'user_name'=>Auth::user()->name,
                'designation'=>(!empty(Auth::user()->title)) ? Auth::user()->title:'Designation Empty',
                'user_type'=>'Initiator',
                'department'=>(!empty(Auth::user()->department)) ? Auth::user()->department:'Department Empty',
                'company'=>(!empty(Auth::user()->company_name)) ? Auth::user()->company_name:'Company Empty',
                'user_status'=>'Initiated',
                'user_action'=>1,
                'date'=>date('d-m-Y h:i:s a')
                ];

        $recommender_id=$inputData['recommender_id'];
        if(!empty($recommender_id)){
             $recommenderInfo=array();
             foreach($recommender_id as $key=>$file){
                $recommenderID=$recommender_id[$key];
                if(!empty($recommenderID) AND Auth::id()!=$recommenderID){
                $recommenderInfo[]=['ticket_id'=>$TicketID,'user_id'=>$recommenderID,'user_type'=>1];               
                $userName=User::where('id', $recommenderID)->first();               

// $thistory[]=['user_id'=>$recommenderID,'user_name'=>$userName->name,'user_type'=>'Recommender','user_status'=>'Pending','user_action'=>0,'date'=>''];


  $thistory[]=[
                'user_id'=>$recommenderID,
                'user_name'=>$userName->name,
                'designation'=>(!empty($userName->title)) ? $userName->title:'Designation Empty',
                'user_type'=>'Recommender',
                'department'=>(!empty($userName->department)) ? $userName->department:'Department Empty',
                'company'=>(!empty($userName->company_name)) ? $userName->company_name:'Company Empty',
                'user_status'=>'Pending',
                'user_action'=>0,
                'date'=>''
                ];


             }else{
                $redirect_msg_wrong='Something is wrong ! avoid select Your email ';
             }

         }
            DB::table('ticket_approve')->insert($recommenderInfo);
         }


     $approver_id=$inputData['approver_id'];
        if(!empty($approver_id)){
             $approverInfo=array();
             foreach($approver_id as $key=>$file){
                $approverID=$approver_id[$key];
                 if(!empty($approverID) AND Auth::id()!=$recommenderID){
                $approverInfo[]=['ticket_id'=>$TicketID,'user_id'=>$approverID,'user_type'=>2];
                   $ApproverName=User::where('id', $approverID)->first();
  // $thistory[]=['user_id'=>$approverID,'user_name'=>$ApproverName->name,'user_type'=>'Approver','user_status'=>'Pending','user_action'=>0,'date'=>''];


    $thistory[]=[
                'user_id'=>$approverID,
                'user_name'=>$ApproverName->name,
                'designation'=>(!empty($ApproverName->title)) ? $ApproverName->title:'Designation Empty',
                'user_type'=>'Approver',
                'department'=>(!empty($ApproverName->department)) ? $ApproverName->department:'Department Empty',
                'company'=>(!empty($ApproverName->company_name)) ? $ApproverName->company_name:'Company Empty',
                'user_status'=>'Pending',
                'user_action'=>0,
                'date'=>''
                ];

             }else{
                $redirect_msg_wrong='Something is wrong ! avoid select Your email';
             }
             }
            DB::table('ticket_approve')->insert($approverInfo);
         }
        $update_thistory=Ticket::find($TicketID);
        $update_thistory->thistory=json_encode($thistory);
        $update_thistory->save();



        // }catch(Exception $e) {
        //     return back()->withError($e->getCode().' : '.$e->getMessage())->withInput();
        // }
// exit();
        return redirect($redirect_to)->with('status', $redirect_msg)->with('error',$redirect_msg_wrong);
    }

public function inbox(Request $request){
        $pageTitle='Inbox';
        $data = [
            'pageTitle' => $pageTitle,
            'listData'  => Ticket::from( 'tickets as t' )
                            ->join('users as u', 'u.id', '=', 't.now_ticket_at')
                            ->join('categorys as c', 'c.id', '=', 't.cat_id')
                            ->join('sub_categorys as sc', 'sc.id', '=', 't.sub_cat_id')
                            // ->where('t.initiator_id', '=', $request->session()->get('userID'))
                            ->where('t.initiator_id', '=',Auth::id())
                            ->orWhere('t.now_ticket_at','=',Auth::id())
                            // ->where('t.tStatus', '=', $tStatus)
                            ->orderBy('t.id', 'DESC')
                            ->paginate(10, ['t.id', 't.tReference_no', 't.tSubject', 'u.name AS user_name', 'c.name AS cat_name', 'sc.name AS sub_cat_name','t.tStatus','t.initiator_id','t.now_ticket_at','t.thistory','t.created_at','t.updated_at']),
            'ctrlName'  => 'ticket',
            'mthdName' =>'inbox',
              ];
        $getStatusList=Self::getStatusList();    
return view('ticket.inbox', compact('data'))->with(['StatusList'=>$getStatusList]);

}


public function rejected(Request $request){    
            $pageTitle = 'Rejected Requests';
            $mthdName  = 'rejected';
            $tStatus=5;       
            $data = [
                'pageTitle' => $pageTitle,
                'listData'  => Ticket::from( 'tickets as t' )
                            ->select('t.id', 't.tReference_no', 't.tSubject', 'u.name AS user_name', 'c.name AS cat_name', 'sc.name AS sub_cat_name','t.tStatus','t.initiator_id','t.now_ticket_at','t.thistory','t.created_at','t.updated_at')
                            ->join('users as u', 'u.id', '=', 't.now_ticket_at')
                            ->join('categorys as c', 'c.id', '=', 't.cat_id')
                            ->join('sub_categorys as sc', 'sc.id', '=', 't.sub_cat_id')
                             ->where('t.initiator_id', '=',Auth::id() )
                            ->where('t.tStatus', '=', $tStatus)
                            ->orderBy('t.id', 'DESC')
                            ->get(),
                'ctrlName'  => 'ticket',
                'mthdName'  => $mthdName
                 ];
             $getStatusList=Self::getStatusList();
        return view('ticket.rejected', compact('data'))->with(['StatusList'=>$getStatusList]);
}

public function drafts(Request $request){    
            $pageTitle = 'Drafts Requests';
            $mthdName  = 'draft';
            $tStatus=1;       
            $data = [
                    'pageTitle' => $pageTitle,
                    'listData'  => Ticket::from( 'tickets as t' )
                                ->select('t.id', 't.tReference_no', 't.tSubject', 'u.name AS user_name', 'c.name AS cat_name', 'sc.name AS sub_cat_name','t.tStatus','t.initiator_id','t.now_ticket_at','t.thistory','t.created_at','t.updated_at')
                                ->join('users as u', 'u.id', '=', 't.now_ticket_at')
                                ->join('categorys as c', 'c.id', '=', 't.cat_id')
                                ->join('sub_categorys as sc', 'sc.id', '=', 't.sub_cat_id')
                                 ->where('t.initiator_id', '=',Auth::id() )
                                ->where('t.tStatus', '=', $tStatus)
                                ->orderBy('t.id', 'DESC')
                                ->get(),
                    'ctrlName'  => 'ticket',
                    'mthdName'  => $mthdName
                     ];
            $getStatusList=Self::getStatusList();
        return view('ticket.drafts', compact('data'))->with(['StatusList'=>$getStatusList]);
    }

public function approved(Request $request){    
            $pageTitle = 'Approved Requests';
            $mthdName  = 'approved';
            $tStatus=4;       
            $data = [
                     'pageTitle' => $pageTitle,
                     'listData'  => Ticket::from( 'tickets as t' )
                        ->select('t.id', 't.tReference_no', 't.tSubject', 'u.name AS user_name', 'c.name AS cat_name', 'sc.name AS sub_cat_name','t.tStatus','t.initiator_id','t.now_ticket_at','t.thistory','t.created_at','t.updated_at')
                        ->join('users as u', 'u.id', '=', 't.now_ticket_at')
                        ->join('categorys as c', 'c.id', '=', 't.cat_id')
                        ->join('sub_categorys as sc', 'sc.id', '=', 't.sub_cat_id')
                        ->where('t.initiator_id', '=',Auth::id() )
                        ->where('t.now_ticket_at', '=',Auth::id() )
                        ->where('t.tStatus', '=', $tStatus)
                        ->orderBy('t.id', 'DESC')
                        ->get(),
                     'ctrlName'  => 'ticket',
                     'mthdName'  => $mthdName
                    ];
            $getStatusList=Self::getStatusList();
        return view('ticket.drafts', compact('data'))->with(['StatusList'=>$getStatusList]);
}


    public function requestInfo(Request $request){
            $tStatus = $request->route()->getAction()['tStatus'];
            $pageTitle = 'Requests for Information';
            $mthdName  = 'request_info';
            $data = [
                    'pageTitle' => $pageTitle,
                    'listData'  => Ticket::from( 'tickets as t' )
                                ->select('t.id', 't.tReference_no', 't.tSubject', 'u.name AS user_name', 'c.name AS cat_name', 'sc.name AS sub_cat_name','t.tStatus','t.initiator_id','t.now_ticket_at','t.thistory','t.created_at','t.updated_at')
                                ->join('users as u', 'u.id', '=', 't.now_ticket_at')
                                ->join('categorys as c', 'c.id', '=', 't.cat_id')
                                ->join('sub_categorys as sc', 'sc.id', '=', 't.sub_cat_id')
                                ->where('t.now_ticket_at', '=',Auth::id() )
                                ->whereIn('t.tStatus', [6,11])
                                // ->orWhere('t.tStatus', '=', 11)
                                ->orderBy('t.id', 'DESC')
                                ->get(),
                    'ctrlName'  => 'ticket',
                    'mthdName'  => $mthdName
                    ];
            $getStatusList=Self::getStatusList();
        return view('ticket.requestInfo', compact('data'))->with(['StatusList'=>$getStatusList]);

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

        if($tStatus == 2){
            $pageTitle = 'Pending Requests';
            $mthdName  = 'pending';

        }elseif($tStatus == 1){
            $pageTitle = 'Draft Requests';
            $mthdName  = 'draft';

        }elseif($tStatus == 6){
            $pageTitle = 'Requests for Information';
            $mthdName  = 'request_info';

        }elseif($tStatus == 5){
            $pageTitle = 'Rejected Requests';
            $mthdName  = 'rejected';

        }else{
            $pageTitle = 'Approved Requests';
            $mthdName  = 'approved';
        }
    

        $data = [
            'pageTitle' => $pageTitle,
            'listData'  => Ticket::from( 'tickets as t' )
                         ->select('t.id', 't.tReference_no', 't.tSubject', 'u.name AS user_name', 'c.name AS cat_name', 'sc.name AS sub_cat_name','t.tStatus','t.initiator_id','t.now_ticket_at','t.thistory','t.created_at','t.updated_at')
                        ->join('users as u', 'u.id', '=', 't.now_ticket_at')
                        ->join('categorys as c', 'c.id', '=', 't.cat_id')
                        ->join('sub_categorys as sc', 'sc.id', '=', 't.sub_cat_id')
                        // ->where('t.initiator_id', '=', $request->session()->get('userID'))
                         ->where('t.initiator_id', '=',Auth::id() )
                        ->whereIn('t.tStatus',[2,6,11,7,8,10])
                        ->orderBy('t.id', 'DESC')
                        ->get(),
                                // ->paginate(10, ['t.id', 't.tReference_no', 't.tSubject', 'u.name AS user_name', 'c.name AS cat_name', 'sc.name AS sub_cat_name','t.tStatus','t.thistory']),
            'ctrlName'  => 'ticket',
            'mthdName'  => $mthdName
        ];
 $getStatusList=Self::getStatusList();
     
        return view('ticket.list', compact('data'))->with(['StatusList'=>$getStatusList]);
    }



    public function ticket_details($id){
          $previousUrl=URL::previous();
          $TicketInfo=Ticket::find($id);
          $subcatList=SubCategory::where('cat_id','=',$TicketInfo->cat_id)->get();
            $recommenderList=User::select('users.name','users.id')
                            ->join('ticket_approve as TA','TA.user_id','=','users.id')
                            ->where('TA.user_type', '=',1)
                            ->where('TA.ticket_id', '=',$id)
                            ->get();
            $approverList=User::select('users.name','users.id')
                            ->join('ticket_approve as TA','TA.user_id','=','users.id')
                            ->where('TA.user_type', '=',2)
                            ->where('TA.ticket_id', '=',$id)
                            ->get();
            // print_r($recommenderList);

            $PreviousComment=DB::table('ticket_historys as TH')
                            ->join('users as UI','TH.action_to','=','UI.id')
                            ->select('TH.*','UI.name as User_name')
                            ->where('TH.ticket_id', '=',$id)
                            ->orderBy('TH.id', 'asc')
                            ->get();


            // exit();
$tStatus=$TicketInfo->tStatus;
  if($tStatus == 2){
            $pageTitle = 'Pending Requests';
            $mthdName  = 'pending';

        }elseif($tStatus == 1){
            $pageTitle = 'Draft Requests';
            $mthdName  = 'draft';

        }elseif($tStatus == 6){
            $pageTitle = 'Requests for Information';
            $mthdName  = 'request_info';

        }elseif($tStatus == 11){
            $pageTitle = 'Requests for Information';
            $mthdName  = 'request_info';

        }


        elseif($tStatus == 5){
            $pageTitle = 'Rejected Requests';
            $mthdName  = 'rejected';

        }else{
            $pageTitle = 'Approved Requests';
            $mthdName  = 'approved';
        }

            $attachmentFile=DB::table('tickets_files')->where('ticket_id', '=',$id)->where('is_delete',0)->get();

    $data = [
            'pageTitle' => 'Details View Request',
            'catList'   => Category::orderBy('id', 'DESC')->pluck('name', 'id'),
            'userList'  => User::pluck('users.name', 'users.id'),
            'ctrlName'  => 'ticket',
            'mthdName'  => $mthdName,
            'TicketInfo'=>$TicketInfo,
            'subcatList'=>$subcatList,
            'previousUrl'=>$previousUrl,
            'approverList'=>$approverList,
            'recommenderList'=>$recommenderList,
            'attachmentFile'=>$attachmentFile,
            'PreviousComment'=>$PreviousComment,
            'CompanyName'=>Company::where('active_date','<=',date('Y-m-d'))
                ->Where(function ($query) {
                $query->whereNull('deactive_date');
                $query->orWhere('deactive_date', '>=', date('Y-m-d'));
                })->pluck('name', 'id')
        ];

        if($TicketInfo->initiator_id==Auth::id() && $TicketInfo->tStatus!=='4' && $TicketInfo->tStatus!=='6'  && $TicketInfo->tStatus!=='11'   && $TicketInfo->tStatus=='2'){
          // return view('ticket.edit_ticket', compact('data'));
             // return view('ticket.request_info_details', compact('data'));   
        return view('ticket.details', compact('data'));
        }elseif($TicketInfo->tStatus=='6' OR $TicketInfo->tStatus=='11'){
if($TicketInfo->tStatus=='11' && $TicketInfo->initiator_id==Auth::id()){
  return view('ticket.details', compact('data'));
}else{
      // return view('ticket.details', compact('data'));
   return view('ticket.request_info_details', compact('data'));   
}
        
        }

        else{
          return view('ticket.details', compact('data'));

        }

      



    }

    public function update_status(Request $request){
        $input=$request->all();
        if(!empty($input['formAction'])){
            $formAction=$input['formAction'];
            if($formAction=='404'){
                $formAction=9;
            }
            $id=$input['id'];
            $result=Ticket::find($id);
            $update           = Ticket::find($id);
            $update->comments =$input['Commentbox'];
             if($formAction=='404'){
            $update->recommender_id =$input['forwardBy'];
        }
            $update->tStatus =$formAction;
            $update->save();
            $insert=new TicketHistory();
            $insert->ticket_id=$result->id;
            $insert->tStatus=$formAction;
            $insert->request_from=$result->initiator_id;
                 if($formAction=='404'){
            $insert->request_to =$input['forwardBy'];
        }else{
             $insert->request_to =Auth::id();
        }         
            $insert->comments=$input['Commentbox'];
            $insert->tDescription=$input['tDescription'];
            $insert->save();

        if($result->tStatus== 2){
                $referenceNo  = uniqid('REF_');
                $redirect_to  = '/request/pending';
                $redirect_msg = 'Successfully request submitted!';
            }
        elseif($result->tStatus== 1){
         $referenceNo  = uniqid('REF_');
                $redirect_to  = '/request/drafts';
                $redirect_msg = 'Successfully request submitted!';
        }

        elseif($result->tStatus== 6){
         $referenceNo  = uniqid('REF_');
                $redirect_to  = '/request/request_info';
                $redirect_msg = 'Successfully request submitted!';
        }
        elseif($result->tStatus== 5){
         $referenceNo  = uniqid('REF_');
                $redirect_to  = '/request/rejected';
                $redirect_msg = 'Successfully request submitted!';
        }
        elseif($result->tStatus== 4){
         $referenceNo  = uniqid('REF_');
                $redirect_to  = '/request/approved';
                $redirect_msg = 'Successfully request submitted!';
        }
        else{
                $referenceNo = NULL;
                $redirect_to  = '/';
                $redirect_msg = 'Request successfully.';
            }
        return redirect($input['previousUrl'])->with('status', $redirect_msg);
               

            }
       

    }



    public function RequestStatusUpdate(Request $request){
        $inputData=$request->all();
        if(!empty($inputData['formAction'])){
            // ================approved=================
              $formAction=$inputData['formAction'];
            if($formAction=='4'){
                $formAction=4;
                $statusUpdate=Approve::where('ticket_id','=',$inputData['id'])->where('user_id','=',Auth::id())->update(['action' => 1]);
                if($statusUpdate){
                    $getFirstValues=Approve::where('ticket_id','=',$inputData['id'])->where('action','=',0)->orderBy('id', 'asc')->first();
                if(!empty($getFirstValues)){
                    $update_nextUser=Ticket::find($inputData['id']);
                    $update_nextUser->now_ticket_at=$getFirstValues->user_id;
                    $update_nextUser->save();
                     $Subject='You have a new request notification'; 
                     $ticNow=$getFirstValues->user_id;
                     $TicketID=$inputData['id'];
                    self::sendMail($TicketID,$Subject,$ticNow);
                }else{
                    $update_nextUser=Ticket::find($inputData['id']);
                    $update_nextUser->now_ticket_at=$update_nextUser->initiator_id;
                    $update_nextUser->tStatus=4;
                    $update_nextUser->save();

                     $Subject='You request for '.$update_nextUser->tSubject.' has been approved ';
                     $ticNow=$update_nextUser->initiator_id;
                     $TicketID=$inputData['id'];
                    self::sendApproedMail($TicketID,$Subject,$ticNow);


                }
                    $update_historys=new TicketHistory();
                    $update_historys->ticket_id=$inputData['id'];
                    $update_historys->tDescription=$inputData['Commentbox'];
                    $update_historys->tStatus=$formAction;
                    $update_historys->action_to=Auth::id();
                    $update_historys->created_by=Auth::id();
                    $update_historys->save();



                    
                }else{
                    $getFirstValues=Approve::where('ticket_id','=',$inputData['id'])->where('action','=',0)->orderBy('id', 'asc')->first();
                      if(!empty($getFirstValues)){
                    $update_nextUser=Ticket::find($inputData['id']);
                    $update_nextUser->now_ticket_at=$getFirstValues->user_id;
                    $update_nextUser->save();
                    $Subject='You have a new request notification'; 
                     $ticNow=$getFirstValues->user_id;
                     $TicketID=$inputData['id'];
                    self::sendMail($TicketID,$Subject,$ticNow);

                }else{
                     $update_nextUser=Ticket::find($inputData['id']);
                    $update_nextUser->now_ticket_at=$update_nextUser->initiator_id;
                    $update_nextUser->tStatus=4;
                    $update_nextUser->save();

                }
                    $update_historys=new TicketHistory();
                    $update_historys->ticket_id=$inputData['id'];
                    $update_historys->tDescription=$inputData['Commentbox'];
                    $update_historys->tStatus=$formAction;
                    $update_historys->action_to=Auth::id();
                    $update_historys->created_by=Auth::id();
                    $update_historys->save();
                }
                    $udateTicketLog=Ticket::find($inputData['id']);
                    $log=json_decode($udateTicketLog->thistory,true);
                    $logInfo=array();
                    foreach($log as $key => $HistoryInfo){

                    if($HistoryInfo['user_id']==Auth::id() && $HistoryInfo['user_action']==0){
                   
                    $userType=$HistoryInfo['user_type'];
                    if($userType=='Recommender'){
                        $userStatus='Recommended';

                    }elseif($userType=='Approver'){
                         $userStatus='Approved';

                    }else{
                     $userStatus='Approved';  
                    }              


                     $logInfo[]=[
                        'user_id'=>$HistoryInfo['user_id'],
                        'user_name'=>$HistoryInfo['user_name'],
                        'designation'=>$HistoryInfo['designation'],
                        'user_type'=>$HistoryInfo['user_type'],
                        'department'=>$HistoryInfo['department'],
                        'company'=>$HistoryInfo['company'],
                        'user_status'=>$userStatus,
                        'user_action'=>1,
                        'date'=>date('d-m-Y h:i:s a')
                        ];

                    }else{

                    if(isset($HistoryInfo['date'])){
                      $habibdate=$HistoryInfo['date'] ? $HistoryInfo['date']:'';
                    }else{
                        $habibdate='';
                    }
                               

             $logInfo[]=[
                        'user_id'=>$HistoryInfo['user_id'],
                        'user_name'=>$HistoryInfo['user_name'],
                        'designation'=>$HistoryInfo['designation'],
                        'user_type'=>$HistoryInfo['user_type'],
                        'department'=>$HistoryInfo['department'],
                        'company'=>$HistoryInfo['company'],
                        'user_status'=>$HistoryInfo['user_status'],
                        'user_action'=>$HistoryInfo['user_action'],
                        'date'=>$habibdate
                     ];

                    }


                    }

 $udateTicketLog->thistory=json_encode($logInfo);
$udateTicketLog->save();

            }

        // ====================rejected=========================
             if($formAction=='5'){
                $formAction=5;
                $statusUpdate=Approve::where('ticket_id','=',$inputData['id'])->where('user_id','=',Auth::id())->update(['action' => 1]);
                if($statusUpdate){
                    $getFirstValues=Approve::where('ticket_id','=',$inputData['id'])->where('action','=',0)->orderBy('id', 'asc')->first();
                    $update_nextUser=Ticket::find($inputData['id']);
                    $update_nextUser->now_ticket_at=$update_nextUser->initiator_id;
                    $update_nextUser->tStatus=5;
                    $update_nextUser->save();
                    $update_historys=new TicketHistory();
                    $update_historys->ticket_id=$inputData['id'];
                    $update_historys->tDescription=$inputData['Commentbox'];
                    $update_historys->tStatus=$formAction;
                    $update_historys->action_to=Auth::id();
                    $update_historys->created_by=Auth::id();
                    $update_historys->save();
                }else{
                    $getFirstValues=Approve::where('ticket_id','=',$inputData['id'])->where('action','=',0)->orderBy('id', 'asc')->first();
                    $update_nextUser=Ticket::find($inputData['id']);
                     $update_nextUser->now_ticket_at=$update_nextUser->initiator_id;
                    $update_nextUser->tStatus=5;
                    $update_nextUser->save();
                    $update_historys=new TicketHistory();
                    $update_historys->ticket_id=$inputData['id'];
                    $update_historys->tDescription=$inputData['Commentbox'];
                    $update_historys->tStatus=$formAction;
                    $update_historys->action_to=Auth::id();
                    $update_historys->created_by=Auth::id();
                    $update_historys->save();
                }

                     $Subject='You request for '.$update_nextUser->tSubject.' has been rejected '; 
                     $ticNow=$update_nextUser->initiator_id;
                     $TicketID=$inputData['id'];
                     self::sendRejectedMail($TicketID,$Subject,$ticNow);
                     $update_nextUser->save();

                     $udateTicketLog=Ticket::find($inputData['id']);
                     $log=json_decode($udateTicketLog->thistory,true);
                     $logInfo=array();
        foreach($log as $key => $HistoryInfo){
        if($HistoryInfo['user_id']==Auth::id() && $HistoryInfo['user_action']==0){
             


     $logInfo[]=[
                'user_id'=>$HistoryInfo['user_id'],
                'user_name'=>$HistoryInfo['user_name'],
                'designation'=>$HistoryInfo['designation'],
                'user_type'=>$HistoryInfo['user_type'],
                'department'=>$HistoryInfo['department'],
                'company'=>$HistoryInfo['company'],
                'user_status'=>'Rejected',
                'user_action'=>1,
                'date'=>date('d-m-Y h:i:s a')
                ];


        }else{
            if(isset($HistoryInfo['date'])){
      $habibdate=$HistoryInfo['date']?$HistoryInfo['date']:'';
    }else{
        $habibdate='';
    }


     $logInfo[]=[
                'user_id'=>$HistoryInfo['user_id'],
                'user_name'=>$HistoryInfo['user_name'],
                'designation'=>$HistoryInfo['designation'],
                'user_type'=>$HistoryInfo['user_type'],
                'department'=>$HistoryInfo['department'],
                'company'=>$HistoryInfo['company'],
                'user_status'=>$HistoryInfo['user_status'],
                'user_action'=>$HistoryInfo['user_action'],
                'date'=>$habibdate
                ];
        }


  }

 $udateTicketLog->thistory=json_encode($logInfo);
$udateTicketLog->save();

            }
  // ====================Request For Info=========================
             if($formAction=='6'){
                $formAction=6;
               
                    $getFirstValues=Approve::where('ticket_id','=',$inputData['id'])->where('action','=',0)->orderBy('id', 'asc')->first();
                    $update_nextUser=Ticket::find($inputData['id']);
                     $update_nextUser->now_ticket_at=$inputData['requestInfoBy'];
                    $update_nextUser->tStatus=$formAction;
                    $update_nextUser->save();
                    $update_historys=new TicketHistory();
                    $update_historys->ticket_id=$inputData['id'];
                    $update_historys->tDescription=$inputData['Commentbox'];
                    $update_historys->tStatus=$formAction;
                    $update_historys->action_to=Auth::id();
                    $update_historys->created_by=Auth::id();
                    $update_historys->save();

                     $Subject='You have a request For Info notification'; 
                     $ticNow=$inputData['requestInfoBy'];
                     $TicketID=$inputData['id'];
                    self::sendMail($TicketID,$Subject,$ticNow);

 					$udateTicketLog=Ticket::find($inputData['id']);
                   $log=json_decode($udateTicketLog->thistory,true);
                   $logInfo=array();

        foreach($log as $key => $HistoryInfo){
            if($HistoryInfo['user_id']==Auth::id() && $HistoryInfo['user_action']==0){

                $logInfo[]=[
                'user_id'=>$HistoryInfo['user_id'],
                'user_name'=>$HistoryInfo['user_name'],
                'designation'=>$HistoryInfo['designation'],
                'user_type'=>$HistoryInfo['user_type'],
                'department'=>$HistoryInfo['department'],
                'company'=>$HistoryInfo['company'],
                'user_status'=>'Request For Info',
                'user_action'=>1,
                'date'=>date('d-m-Y h:i:s a')
                ];

                $userInfo=User::where('id',$inputData['requestInfoBy'])->first();
 

                 $logInfo[]=[
                'user_id'=>$userInfo->id,
                'user_name'=>$userInfo->name,
                'designation'=>(!empty($userInfo->title)) ? $userInfo->title:'Designation Empty',
                'user_type'=>'Request For Info User',
                'department'=>(!empty($userInfo->department)) ? $userInfo->department:'Department Empty',
                'company'=>(!empty($userInfo->company_name)) ? $userInfo->company_name:'Company Empty',
                'user_status'=>'Pending',
                'user_action'=>0,
                'date'=>''
                ];

            }else{
                 if(isset($HistoryInfo['date'])){
                         $habibdate=$HistoryInfo['date']?$HistoryInfo['date']:'';
                    }else{
                        $habibdate='';
                    }
             
            $logInfo[]=[
                'user_id'=>$HistoryInfo['user_id'],
                'user_name'=>$HistoryInfo['user_name'],
                'designation'=>$HistoryInfo['designation'],
                'user_type'=>$HistoryInfo['user_type'],
                'department'=>$HistoryInfo['department'],
                'company'=>$HistoryInfo['company'],
                'user_status'=>$HistoryInfo['user_status'],
                'user_action'=>$HistoryInfo['user_action'],
                'date'=>$habibdate
                ];

            }


         }

        $udateTicketLog->thistory=json_encode($logInfo);
        $udateTicketLog->save();


                    // $update_nextUser->save();

//                     $udateTicketLog=Ticket::find($inputData['id']);
//  $log=json_decode($udateTicketLog->thistory,true);
// $logInfo=array();
//   foreach($log as $key => $HistoryInfo){
//     if($HistoryInfo['user_id']==Auth::id() && $HistoryInfo['user_action']==0){
//     $logInfo[]=['user_id'=>$HistoryInfo['user_id'],'user_name'=>$HistoryInfo['user_name'],'user_type'=>$HistoryInfo['user_type'],'user_status'=>'Request For Info','user_action'=>1];
//     $userInfo=User::where('id',$inputData['requestInfoBy'])->first();
//      $logInfo[]=['user_id'=>$userInfo->id,'user_name'=>$userInfo->name,'user_type'=>'Request Info','user_status'=>'Request For Info','user_action'=>0];
// }else{
//  $logInfo[]=['user_id'=>$HistoryInfo['user_id'],'user_name'=>$HistoryInfo['user_name'],'user_type'=>$HistoryInfo['user_type'],'user_status'=>$HistoryInfo['user_status'],'user_action'=>$HistoryInfo['user_action']];
// }


//   }

//  $udateTicketLog->thistory=json_encode($logInfo);
// $udateTicketLog->save();
               
            }

              // ====================Request For Info Back=========================
             if($formAction=='11'){
                $formAction=11;
               
                    $getRequestInfoByFirstValues=TicketHistory::where('ticket_id','=',$inputData['id'])->orderBy('id', 'DESC')->first();
                    // $getRequestInfoByFirstValues=TicketHistory::where('ticket_id','=',$inputData['id'])->orderBy('id', 'DESC')->skip(1)->take(1)->get();
                    $getRequestInfoByFirstValues->created_by;
                    $update_nextUser=Ticket::find($inputData['id']);
                    $update_nextUser->now_ticket_at=$getRequestInfoByFirstValues->created_by;
                    $update_nextUser->tStatus=$formAction;
                    $update_nextUser->save();
                    $update_historys=new TicketHistory();
                    $update_historys->ticket_id=$inputData['id'];
                    $update_historys->tDescription=$inputData['Commentbox'];
                    $update_historys->tStatus=$formAction;
                    $update_historys->action_to=Auth::id();
                    $update_historys->created_by=Auth::id();
                    $update_historys->save();  
                    $Subject='You have a request notification'; 
                     $ticNow=$getRequestInfoByFirstValues->created_by;
                     $TicketID=$inputData['id'];
                    self::sendMail($TicketID,$Subject,$ticNow);

 $udateTicketLog=Ticket::find($inputData['id']);
                   $log=json_decode($udateTicketLog->thistory,true);
                   $logInfo=array();

        foreach($log as $key => $HistoryInfo){
            if($HistoryInfo['user_id']==Auth::id() && $HistoryInfo['user_action']==0){

                $logInfo[]=[
                'user_id'=>$HistoryInfo['user_id'],
                'user_name'=>$HistoryInfo['user_name'],
                'designation'=>$HistoryInfo['designation'],
                'user_type'=>$HistoryInfo['user_type'],
                'department'=>$HistoryInfo['department'],
                'company'=>$HistoryInfo['company'],
                'user_status'=>'Request For Info Back',
                'user_action'=>1,
                'date'=>date('d-m-Y h:i:s a')
                ];

                // $userInfo=User::where('id',$getRequestInfoByFirstValues->created_by)->first();
 

                //  $logInfo[]=[
                // 'user_id'=>$userInfo->id,
                // 'user_name'=>$userInfo->name,
                // 'designation'=>(!empty($userInfo->designation)) ? $userInfo->designation:'Designation Empty',
                // 'user_type'=>'Request For Info User',
                // 'department'=>(!empty($userInfo->department)) ? $userInfo->department:'Department Empty',
                // 'company'=>(!empty($userInfo->company)) ? $userInfo->company:'Company Empty',
                // 'user_status'=>'Pending',
                // 'user_action'=>0,
                // 'date'=>''
                // ];

            }else{
                 if(isset($HistoryInfo['date'])){
                         $habibdate=$HistoryInfo['date']?$HistoryInfo['date']:'';
                    }else{
                        $habibdate='';
                    }
             
            $logInfo[]=[
                'user_id'=>$HistoryInfo['user_id'],
                'user_name'=>$HistoryInfo['user_name'],
                'designation'=>$HistoryInfo['designation'],
                'user_type'=>$HistoryInfo['user_type'],
                'department'=>$HistoryInfo['department'],
                'company'=>$HistoryInfo['company'],
                'user_status'=>$HistoryInfo['user_status'],
                'user_action'=>$HistoryInfo['user_action'],
                'date'=>$habibdate
                ];

            }


         }

        $udateTicketLog->thistory=json_encode($logInfo);
        $udateTicketLog->save();


                    // $update_nextUser->save();
//                     $udateTicketLog=Ticket::find($inputData['id']);
//  $log=json_decode($udateTicketLog->thistory,true);
// $logInfo=array();
//   foreach($log as $key => $HistoryInfo){
//     if($HistoryInfo['user_id']==Auth::id() && $HistoryInfo['user_action']==0){
//     $logInfo[]=['user_id'=>$HistoryInfo['user_id'],'user_name'=>$HistoryInfo['user_name'],'user_type'=>$HistoryInfo['user_type'],'user_status'=>'Request Info  back','user_action'=>1];
//     $userInfo=User::where('id',$getRequestInfoByFirstValues->created_by)->first();
//      $logInfo[]=['user_id'=>$userInfo->id,'user_name'=>$userInfo->name,'user_type'=>'Request Info Back','user_status'=>'Request For Info Back','user_action'=>0];
// }else{
//  $logInfo[]=['user_id'=>$HistoryInfo['user_id'],'user_name'=>$HistoryInfo['user_name'],'user_type'=>$HistoryInfo['user_type'],'user_status'=>$HistoryInfo['user_status'],'user_action'=>$HistoryInfo['user_action']];
// }


//   }

//  $udateTicketLog->thistory=json_encode($logInfo);
// $udateTicketLog->save();

               
            }


        // ====================Forward=========================
             if($formAction=='7'){
                $formAction=7;
                $statusUpdate=Approve::where('ticket_id','=',$inputData['id'])->where('user_id','=',Auth::id())->update(['action' => 1]);
                if($statusUpdate){
                    $getFirstValues=Approve::where('ticket_id','=',$inputData['id'])->where('action','=',0)->orderBy('id', 'asc')->first();
                    $update_nextUser=Ticket::find($inputData['id']);
                    $update_nextUser->now_ticket_at=$inputData['forwardUser'];
                    $update_nextUser->tStatus=7;
                    $update_nextUser->save();
                    $update_historys=new TicketHistory();
                    $update_historys->ticket_id=$inputData['id'];
                    $update_historys->tDescription=$inputData['Commentbox'];
                    $update_historys->tStatus=$formAction;
                    $update_historys->action_to=Auth::id();
                    $update_historys->created_by=Auth::id();
                    $update_historys->save();
                }else{
                    $getFirstValues=Approve::where('ticket_id','=',$inputData['id'])->where('action','=',0)->orderBy('id', 'asc')->first();
                    $update_nextUser=Ticket::find($inputData['id']);
                    $update_nextUser->now_ticket_at=$inputData['forwardUser'];
                    $update_nextUser->tStatus=7;
                    $update_nextUser->save();
                    $update_historys=new TicketHistory();
                    $update_historys->ticket_id=$inputData['id'];
                    $update_historys->tDescription=$inputData['Commentbox'];
                    $update_historys->tStatus=$formAction;
                    $update_historys->action_to=Auth::id();
                    $update_historys->created_by=Auth::id();
                    $update_historys->save();
                } 

                   $udateTicketLog=Ticket::find($inputData['id']);
                   $log=json_decode($udateTicketLog->thistory,true);
                   $logInfo=array();

        foreach($log as $key => $HistoryInfo){
            if($HistoryInfo['user_id']==Auth::id() && $HistoryInfo['user_action']==0){

                // $logInfo[]=[
                // 'user_id'=>$HistoryInfo['user_id'],
                // 'user_name'=>$HistoryInfo['user_name'],
                // 'user_type'=>$HistoryInfo['user_type'],
                // 'user_status'=>'Forward',
                // 'user_action'=>1,
                // 'date'=>date('d-m-Y H:i:s')
                // ];

                $logInfo[]=[
                'user_id'=>$HistoryInfo['user_id'],
                'user_name'=>$HistoryInfo['user_name'],
                'designation'=>$HistoryInfo['designation'],
                'user_type'=>$HistoryInfo['user_type'],
                'department'=>$HistoryInfo['department'],
                'company'=>$HistoryInfo['company'],
                'user_status'=>'Forwarded',
                'user_action'=>1,
                'date'=>date('d-m-Y h:i:s a')
                ];

                $userInfo=User::where('id',$inputData['forwardUser'])->first();
                // $logInfo[]=[
                // 'user_id'=>$userInfo->id,
                // 'user_name'=>$userInfo->name,
                // 'user_type'=>'Forward',
                // 'user_status'=>'Forward User',
                // 'user_action'=>0,
                // 'date'=>''
                // ];

                 $logInfo[]=[
                'user_id'=>$userInfo->id,
                'user_name'=>$userInfo->name,
                'designation'=>(!empty($userInfo->title)) ? $userInfo->title:'Designation Empty',
                'user_type'=>'Forwarded User',
                'department'=>(!empty($userInfo->department)) ? $userInfo->department:'Department Empty',
                'company'=>(!empty($userInfo->company_name)) ? $userInfo->company_name:'Company Empty',
                'user_status'=>'Pending',
                'user_action'=>0,
                'date'=>''
                ];

            }else{
                 if(isset($HistoryInfo['date'])){
                         $habibdate=$HistoryInfo['date']?$HistoryInfo['date']:'';
                    }else{
                        $habibdate='';
                    }
                // $logInfo[]=[
                // 'user_id'=>$HistoryInfo['user_id'],
                // 'user_name'=>$HistoryInfo['user_name'],
                // 'user_type'=>$HistoryInfo['user_type'],
                // 'user_status'=>$HistoryInfo['user_status'],
                // 'user_action'=>$HistoryInfo['user_action'],
                // 'date'=>$habibdate
                // ];
            $logInfo[]=[
                'user_id'=>$HistoryInfo['user_id'],
                'user_name'=>$HistoryInfo['user_name'],
                'designation'=>$HistoryInfo['designation'],
                'user_type'=>$HistoryInfo['user_type'],
                'department'=>$HistoryInfo['department'],
                'company'=>$HistoryInfo['company'],
                'user_status'=>$HistoryInfo['user_status'],
                'user_action'=>$HistoryInfo['user_action'],
                'date'=>$habibdate
                ];

            }


         }

        $udateTicketLog->thistory=json_encode($logInfo);
        $udateTicketLog->save();

        $Subject='You have a new request notification'; 
        $ticNow=$inputData['forwardUser'];
        $TicketID=$inputData['id'];
        self::sendMail($TicketID,$Subject,$ticNow);
        // $update_nextUser->save();

}
     // ====================Appoved And Forward=========================
             if($formAction=='504'){
                $formAction=8;
                   $statusUpdate=Approve::where('ticket_id','=',$inputData['id'])->where('user_id','=',Auth::id())->update(['action' => 1]);
                if($statusUpdate){
                    $getFirstValues=Approve::where('ticket_id','=',$inputData['id'])->where('action','=',0)->orderBy('id', 'asc')->first();
                    $update_nextUser=Ticket::find($inputData['id']);
                    $update_nextUser->now_ticket_at=$inputData['forwardBy'];
                    $update_nextUser->tStatus=7;
                    $update_nextUser->save();
                    $update_historys=new TicketHistory();
                    $update_historys->ticket_id=$inputData['id'];
                    $update_historys->tDescription=$inputData['Commentbox'];
                    $update_historys->tStatus=$formAction;
                    $update_historys->action_to=Auth::id();
                    $update_historys->created_by=Auth::id();
                    $update_historys->save();
                }else{
                    $getFirstValues=Approve::where('ticket_id','=',$inputData['id'])->where('action','=',0)->orderBy('id', 'asc')->first();
                    $update_nextUser=Ticket::find($inputData['id']);
                     $update_nextUser->now_ticket_at=$inputData['forwardBy'];
                    $update_nextUser->tStatus=7;
                    $update_nextUser->save();
                    $update_historys=new TicketHistory();
                    $update_historys->ticket_id=$inputData['id'];
                    $update_historys->tDescription=$inputData['Commentbox'];
                    $update_historys->tStatus=$formAction;
                    $update_historys->action_to=Auth::id();
                    $update_historys->created_by=Auth::id();
                    $update_historys->save();
                } 

                  $udateTicketLog=Ticket::find($inputData['id']);
                  $log=json_decode($udateTicketLog->thistory,true);
                  $logInfo=array();
        foreach($log as $key => $HistoryInfo){
            if($HistoryInfo['user_id']==Auth::id() && $HistoryInfo['user_action']==0){

                // $logInfo[]=[
                // 'user_id'=>$HistoryInfo['user_id'],
                // 'user_name'=>$HistoryInfo['user_name'],
                // 'user_type'=>$HistoryInfo['user_type'],
                // 'user_status'=>'Appoved And Forward',
                // 'user_action'=>1,
                // 'date'=>date('d-m-Y H:i:s')
                // ];
                 $userType=$HistoryInfo['user_type'];
                    if($userType=='Recommender'){
                        $userStatus='Approved and Forwarded';

                    }elseif($userType=='Approver'){
                         $userStatus='Approved and Forwarded';

                    }else{
                     $userStatus='Approved and Forwarded';  
                    }  
   $logInfo[]=[
                'user_id'=>$HistoryInfo['user_id'],
                'user_name'=>$HistoryInfo['user_name'],
                'designation'=>$HistoryInfo['designation'],
                'user_type'=>$HistoryInfo['user_type'],
                'department'=>$HistoryInfo['department'],
                'company'=>$HistoryInfo['company'],
                'user_status'=>$userStatus,
                'user_action'=>1,
                'date'=>date('d-m-Y h:i:s a')
                ];


                $userInfo=User::where('id',$inputData['forwardBy'])->first();

                // $logInfo[]=[
                // 'user_id'=>$userInfo->id,
                // 'user_name'=>$userInfo->name,
                // 'user_type'=>'Forward',
                // 'user_status'=>'Forward User',
                // 'user_action'=>0,
                // 'date'=>''
                // ];

                 $logInfo[]=[
                'user_id'=>$userInfo->id,
                'user_name'=>$userInfo->name,
                'designation'=>(!empty($userInfo->title)) ? $userInfo->title:'Designation Empty',
                'user_type'=>'Forwarded User',
                'department'=>(!empty($userInfo->department)) ? $userInfo->department:'Department Empty',
                'company'=>(!empty($userInfo->company_name)) ? $userInfo->company_name:'Compay Name Empty',
                'user_status'=>'Pending',
                'user_action'=>0,
                'date'=>''
                ];

                }else{
                if(isset($HistoryInfo['date'])){

                $habibdate=$HistoryInfo['date'] ? $HistoryInfo['date']:'';

                    }else{

                $habibdate='';

                }
                // $logInfo[]=[
                //     'user_id'=>$HistoryInfo['user_id'],
                //     'user_name'=>$HistoryInfo['user_name'],
                //     'user_type'=>$HistoryInfo['user_type'],
                //     'user_status'=>$HistoryInfo['user_status'],
                //     'user_action'=>$HistoryInfo['user_action'],
                //     'date'=>$habibdate
                // ];

        $logInfo[]=[
                'user_id'=>$HistoryInfo['user_id'],
                'user_name'=>$HistoryInfo['user_name'],
                'designation'=>$HistoryInfo['designation'],
                'user_type'=>$HistoryInfo['user_type'],
                'department'=>$HistoryInfo['department'],
                'company'=>$HistoryInfo['company'],
                'user_status'=>$HistoryInfo['user_status'],
                'user_action'=>$HistoryInfo['user_action'],
                'date'=>$habibdate
                ];


                }

                $Subject='You have a new request notification'; 
                $ticNow=$inputData['forwardBy'];
                $TicketID=$inputData['id'];
                self::sendMail($TicketID,$Subject,$ticNow);
                // $update_nextUser->save();

                }

 $udateTicketLog->thistory=json_encode($logInfo);
$udateTicketLog->save();

            }
// ====================Appoved And Acknowledgement=========================
             if($formAction=='404'){
                $formAction=9;
                $statusUpdate=Approve::where('ticket_id','=',$inputData['id'])->where('user_id','=',Auth::id())->update(['action' => 1]);
                if($statusUpdate){
                    $getFirstValues=Approve::where('ticket_id','=',$inputData['id'])->where('action','=',0)->orderBy('id', 'asc')->first();
                    if(!empty($getFirstValues)){
                    $update_nextUser=Ticket::find($inputData['id']);
                    $update_nextUser->now_ticket_at=$getFirstValues->user_id;
                    $update_nextUser->tStatus=9;
                    $update_nextUser->save();
                    $mailUser=$getFirstValues->user_id;
                }else{
                   $update_nextUser=Ticket::find($inputData['id']);
                    $update_nextUser->now_ticket_at=$update_nextUser->initiator_id;
                    $update_nextUser->tStatus=4;
                    $update_nextUser->save();  
                    $mailUser=$update_nextUser->initiator_id;
                }
                    $update_historys=new TicketHistory();
                    $update_historys->ticket_id=$inputData['id'];
                    $update_historys->tDescription=$inputData['Commentbox'];
                    $update_historys->tStatus=4;
                    $update_historys->action_to=Auth::id();
                    $update_historys->created_by=Auth::id();
                    $update_historys->save();
                    $update_notification_historys=new TicketHistory();
                    $update_notification_historys->ticket_id=$inputData['id'];
                    $update_notification_historys->tDescription='Notification';
                    $update_notification_historys->tStatus=$formAction;
                    $update_notification_historys->action_to=$inputData['AcknowledgementBy'];
                    $update_notification_historys->created_by=Auth::id();
                    $update_notification_historys->save();
                }else{
                    $getFirstValues=Approve::where('ticket_id','=',$inputData['id'])->where('action','=',0)->orderBy('id', 'asc')->first();
                     if(!empty($getFirstValues)){
                    $update_nextUser=Ticket::find($inputData['id']);
                    $update_nextUser->now_ticket_at=$getFirstValues->user_id;
                    $update_nextUser->tStatus=9;
                    $update_nextUser->save();
                    $mailUser=$getFirstValues->user_id;
                }else{
                    $update_nextUser=Ticket::find($inputData['id']);
                    $update_nextUser->now_ticket_at=$update_nextUser->initiator_id;
                    $update_nextUser->tStatus=4;
                    $update_nextUser->save();
                    $mailUser=$update_nextUser->initiator_id;

                    }
                    $update_historys=new TicketHistory();
                    $update_historys->ticket_id=$inputData['id'];
                    $update_historys->tDescription=$inputData['Commentbox'];
                    $update_historys->tStatus=4;
                    $update_historys->action_to=Auth::id();
                    $update_historys->created_by=Auth::id();
                    $update_historys->save();
                    $update_notification_historys=new TicketHistory();
                    $update_notification_historys->ticket_id=$inputData['id'];
                    $update_notification_historys->tDescription='Notification';
                    $update_notification_historys->tStatus=$formAction;
                    $update_notification_historys->action_to=$inputData['AcknowledgementBy'];
                    $update_notification_historys->created_by=Auth::id();
                    $update_notification_historys->save();
                }


                 $udateTicketLog=Ticket::find($inputData['id']);
                 $log=json_decode($udateTicketLog->thistory,true);
                 $logInfo=array();
  foreach($log as $key => $HistoryInfo){
    if($HistoryInfo['user_id']==Auth::id() && $HistoryInfo['user_action']==0){



         // $logInfo[]=[
         // 'user_id'=>$HistoryInfo['user_id'],
         // 'user_name'=>$HistoryInfo['user_name'],
         // 'user_type'=>$HistoryInfo['user_type'],
         // 'user_status'=>'Appoved And Acknowledgement',
         // 'user_action'=>1,
         // 'date'=>date('d-m-Y H:i:s')
         // ];

                $userType=$HistoryInfo['user_type'];
                    if($userType=='Recommender'){
                        $userStatus='Recommended';

                    }elseif($userType=='Approver'){
                         $userStatus='Approved';

                    }else{
                     $userStatus='Approved';  
                    }
        
            $logInfo[]=[
                'user_id'=>$HistoryInfo['user_id'],
                'user_name'=>$HistoryInfo['user_name'],
                'designation'=>$HistoryInfo['designation'],
                'user_type'=>$HistoryInfo['user_type'],
                'department'=>$HistoryInfo['department'],
                'company'=>$HistoryInfo['company'],
                'user_status'=>$userStatus,
                'user_action'=>1,
                'date'=>date('d-m-Y h:i:s a')
                ];

         $userInfo=User::where('id',$inputData['AcknowledgementBy'])->first();

            // $logInfo[]=[
            // 'user_id'=>$userInfo->id,
            // 'user_name'=>$userInfo->name,
            // 'user_type'=>'Forward',
            // 'user_status'=>'Acknowledgement User',
            // 'user_action'=>0,
            // 'date'=>''
            // ];

                 $logInfo[]=[
                    'user_id'=>$userInfo->id,
                    'user_name'=>$userInfo->name,
                    'designation'=>(!empty($userInfo->title)) ? $userInfo->title:'Designation Empty',
                    'user_type'=>'Notification Reciver',
                    'department'=>(!empty($userInfo->department)) ? $userInfo->department:'Department Empty',
                    'company'=>(!empty($userInfo->company_name)) ? $userInfo->company_name:'Company Name Empty',
                    'user_status'=>'Notified',
                    'user_action'=>1,
                    'date'=>date('d-m-Y h:i:s a')
                    ];
            }else{

         if(isset($HistoryInfo['date'])){
                 $habibdate=$HistoryInfo['date']?$HistoryInfo['date']:'';
             }else{
                $habibdate='';
            }

                // $logInfo[]=[
                // 'user_id'=>$HistoryInfo['user_id'],
                // 'user_name'=>$HistoryInfo['user_name'],
                // 'user_type'=>$HistoryInfo['user_type'],
                // 'user_status'=>$HistoryInfo['user_status'],
                // 'user_action'=>$HistoryInfo['user_action'],
                // 'date'=>$habibdate
                // ];
                $logInfo[]=[
                'user_id'=>$HistoryInfo['user_id'],
                'user_name'=>$HistoryInfo['user_name'],
                'designation'=>$HistoryInfo['designation'],
                'user_type'=>$HistoryInfo['user_type'],
                'department'=>$HistoryInfo['department'],
                'company'=>$HistoryInfo['company'],
                'user_status'=>$HistoryInfo['user_status'],
                'user_action'=>$HistoryInfo['user_action'],
                'date'=>$habibdate
                ];
         }

            $Subject='You have a new request notification'; 
            $ticNow=$mailUser;
            $TicketID=$inputData['id'];
            self::sendMail($TicketID,$Subject,$ticNow);

        // $update_nextUser->save();


            }
// exit();
            $udateTicketLog->thistory=json_encode($logInfo);
            $udateTicketLog->save();

            }


                }else{
                echo "select Action";
                }
                    $TicketID=$inputData['id'];
                if($request->hasfile('tFile')){
                    $fileData=array();
                    foreach($request->file('tFile') as $file){
                    $fileName  = Auth::id().'-'.time().'-'.$file->getClientOriginalName();
                    $fileType=$file->getClientOriginalExtension();
                    
                    // $destinationPath = public_path('/upload/ticket_file');
                    // $file->move($destinationPath, $fileName);  
                    // $fileData[]=['ticket_id'=>$TicketID,'file_name'=>$fileName,'file_type'=>$fileType];

                    $destinationPath = public_path('/upload/ticket_file/'.date('Y'));
                    $file->move($destinationPath, $fileName);  
                    $folder='upload/ticket_file/'.date('Y');
                    $fileData[]=['ticket_id'=>$TicketID,'file_name'=>$fileName,'file_type'=>$fileType,'folder'=>$folder];

                            }
                    DB::table('tickets_files')->insert($fileData);
                         }        
                    $redirect_msg='Successfully Update';
                return redirect('request/inbox')->with('status', $redirect_msg);


         }

        public function getStatusList(){
                            $statusList[1]='Save as Draft ';
                            $statusList[2]='Pending';
                            $statusList[3]='Draft by Approver ';
                            $statusList[4]='Approved';
                            $statusList[5]='Rejected';
                            $statusList[6]='Request for Info';
                            $statusList[7]='Forward';
                            $statusList[8]='Appoved And Forward';
                            $statusList[9]='Appoved And Acknowledgement';
                            $statusList[10]='Disable';
                            $statusList[11]='Pending';
                            return $statusList;
            }


        public function DraftEdit(Request $request,$id=0){

     $previousUrl=URL::previous();
          $TicketInfo=Ticket::find($id);
          $subcatList=SubCategory::where('cat_id','=',$TicketInfo->cat_id)->where('active_date','<=',date('Y-m-d'))
                ->Where(function ($query) {
                $query->whereNull('deactive_date');
                $query->orWhere('deactive_date', '>=', date('Y-m-d'));
                })->get();
            $recommenderList=User::select('users.*')
            ->join('ticket_approve as TA','TA.user_id','=','users.id')
             ->where('TA.user_type', '=',1)
             ->where('TA.ticket_id', '=',$id)
            ->get();
            // print_r($recommenderList);

            // exit();
            $approverList=User::select('users.*')
            ->join('ticket_approve as TA','TA.user_id','=','users.id')
             ->where('TA.user_type', '=',2)
             ->where('TA.ticket_id', '=',$id)
            ->get();
            // print_r($recommenderList);

            $PreviousComment=DB::table('ticket_historys as TH')
            ->join('users as UI','TH.action_to','=','UI.id')
            ->select('TH.*','UI.name as User_name')
            ->where('TH.ticket_id', '=',$id)
            ->orderBy('TH.id', 'asc')
            ->get();


            // exit();
$tStatus=$TicketInfo->tStatus;
  if($tStatus == 2){
            $pageTitle = 'Pending Requests';
            $mthdName  = 'pending';

        }elseif($tStatus == 1){
            $pageTitle = 'Draft Requests';
            $mthdName  = 'draft';

        }elseif($tStatus == 6){
            $pageTitle = 'Requests for Information';
            $mthdName  = 'request_info';

        }elseif($tStatus == 11){
            $pageTitle = 'Requests for Information';
            $mthdName  = 'request_info';

        }


        elseif($tStatus == 5){
            $pageTitle = 'Rejected Requests';
            $mthdName  = 'rejected';

        }else{
            $pageTitle = 'Approved Requests';
            $mthdName  = 'approved';
        }

            $attachmentFile=DB::table('tickets_files')->where('ticket_id', '=',$id)->get();

    $data = [
            'pageTitle' => 'Details View Request',
            'catList'   => Category::where('active_date','<=',date('Y-m-d'))
                ->Where(function ($query) {
                $query->whereNull('deactive_date');
                $query->orWhere('deactive_date', '>=', date('Y-m-d'));
                })->orderBy('name')->pluck('name', 'id'),
            'userList'  => User::pluck('users.name', 'users.id'),
            'ctrlName'  => 'ticket',
            'mthdName'  => $mthdName,
            'TicketInfo'=>$TicketInfo,
            'subcatList'=>$subcatList,
            'previousUrl'=>$previousUrl,
            'approverList'=>$approverList,
            'recommenderList'=>$recommenderList,
            'attachmentFile'=>$attachmentFile,
            'PreviousComment'=>$PreviousComment,
            'CompanyName'=>Company::where('active_date','<=',date('Y-m-d'))
                ->Where(function ($query) {
                $query->whereNull('deactive_date');
                $query->orWhere('deactive_date', '>=', date('Y-m-d'));
                })->orderBy('name')->pluck('name', 'id'),
            'userList2'=>User::get()
        ];

       
        return view('ticket.edit_draft_request', compact('data'));
       


        }


        public function DraftDelete(Request $request,$id){
try{
            $id = Crypt::decryptString($id);

            $findTicket=Ticket::findOrFail($id);
            if(!empty($findTicket)){
             $TicketID=$findTicket->id;

            $approverData = DB::table('ticket_approve')->where('ticket_id', $TicketID)->delete();
            $FileData = DB::table('tickets_files')->where('ticket_id', $TicketID)->delete();
            $HistorysData = DB::table('ticket_historys')->where('ticket_id', $TicketID)->delete();
            $TicketData = Ticket::where('id', $TicketID)->delete();

            }else{
                    $redirect_msg='Something wrong try later';
                return Redirect::back()->with('error', $redirect_msg);
            }
                 $redirect_msg='Successfully Deleted';
                return Redirect::back()->with('status', $redirect_msg);
        }catch(Exception $e) {
            return back()->withError($e->getCode().' : '.$e->getMessage())->withInput();
        }

        }
        public function deleteOldFile(Request $request){
            $inputData=$request->all();
             $id=$inputData['FileId'];
            $update=DB::table('tickets_files')->where('id',$id)->update(['is_delete'=>1]);
            $result=array();
            if($update){

              $result=['Result'=>'200','ms'=>'Delete Done','id'=>$id];
            }else{
               $result=['Result'=>'100','ms'=>'Delete Done','id'=>$id];  
            }
              return response($result);
    

        }

        public function UpdateDraftRequest(Request $request){
             $inputData=$request->all();
             // echo "<pre>";
             // print_r($inputData);
             $this->validate($request, [
        'cat_id' => 'required|max:10',
        'sub_cat_id' => 'required',
        'tSubject' => 'required',
        'tDescription' => 'required',
       
        ]);
// exit();

        if($request['tStatus'] == 2){
            // $referenceNo  = uniqid('REF_');
            $companyInfo=Company::where('id','=',$inputData['company_id'])->select('short_name')->first();
            $categoryInfo=Category::where('id','=',$inputData['cat_id'])->select('name')->first();
            $subCategoryInfo=SubCategory::where('id','=',$inputData['sub_cat_id'])->select('name')->first();
            $requestId=Ticket::whereMonth('created_at', date('m'))->count();
            $requestId=$requestId+1;           
            $referenceNo  = $companyInfo->short_name.'-'.$categoryInfo->name.'-'.$subCategoryInfo->name.'-'.date('Ym').$requestId;//uniqid('REF_');
            $redirect_to  = '/request/pending';
            $redirect_msg = 'Successfully new request submitted!';
        }else{
            $referenceNo = NULL;
            $redirect_to  = '/request/drafts';
            $redirect_msg = 'Request successfully saved as draft.';
        }   

      
        $ticketRequest=Ticket::find($inputData['id']);
        $ticketRequest->tReference_no=$referenceNo;
        $ticketRequest->company_id=$inputData['company_id'];
        $ticketRequest->cat_id=$inputData['cat_id'];
        $ticketRequest->sub_cat_id=$inputData['sub_cat_id'];
        $ticketRequest->initiator_id=Auth::id();//$request->session()->get('userID')        
        $ticketRequest->tSubject=$inputData['tSubject'];
        $ticketRequest->tDescription=$inputData['tDescription'];
        $ticketRequest->tStatus=$inputData['tStatus'];
        $ticketRequest->now_ticket_at=$inputData['recommender_id'][0];
        $ticketRequest->save();
       $TicketID=$ticketRequest->id;        // $TicketID=2;
         if($request->hasfile('tFile')){
            $fileData=array();

    foreach($request->file('tFile') as $file){
        $fileName  = Auth::id().'-'.time().'-'.$file->getClientOriginalName();
        $fileType=$file->getClientOriginalExtension();
        // $destinationPath = public_path('/upload/ticket_file');
        // $file->move($destinationPath, $fileName);  
        // $fileData[]=['ticket_id'=>$TicketID,'file_name'=>$fileName,'file_type'=>$fileType];
        $destinationPath = public_path('/upload/ticket_file/'.date('Y'));
        $file->move($destinationPath, $fileName);  
        $folder='upload/ticket_file/'.date('Y');
        $fileData[]=['ticket_id'=>$TicketID,'file_name'=>$fileName,'file_type'=>$fileType,'folder'=>$folder];
            }
        DB::table('tickets_files')->insert($fileData);
         }    

 DB::table('ticket_approve')->where('ticket_id',$TicketID)->where('user_type',1)->delete(); 
     $recommender_id=$inputData['recommender_id'];
        if(!empty($recommender_id)){
             $recommenderInfo=array();
             foreach($recommender_id as $key=>$file){
                $recommenderID=$recommender_id[$key];
                $recommenderInfo[]=['ticket_id'=>$TicketID,'user_id'=>$recommenderID,'user_type'=>1];

             }
            DB::table('ticket_approve')->insert($recommenderInfo);
         }
          DB::table('ticket_approve')->where('ticket_id',$TicketID)->where('user_type',2)->delete(); 
     $approver_id=$inputData['approver_id'];
        if(!empty($approver_id)){
             $approverInfo=array();
             foreach($approver_id as $key=>$file){
                $approverID=$approver_id[$key];
                $approverInfo[]=['ticket_id'=>$TicketID,'user_id'=>$approverID,'user_type'=>2];
             }
            DB::table('ticket_approve')->insert($approverInfo);
         }


          return redirect($redirect_to)->with('status', $redirect_msg);

        }


        public function searchAdUser(Request $request){
           $input=$request->all();
           $search=$input['term'];
           $users = DB::table('users')
           // ->select('name','id')
                // ->where('id', '!=',Auth::id())
                ->where('name', 'like',"%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('title', 'like', "%{$search}%")
                ->orWhere('department', 'like', "%{$search}%")
                ->orWhere('telephonenumber', 'like', "%{$search}%")   
                ->take(10)                                    
                ->get();
            return json_encode($users);
        } 
       public function searchAdUserInModal(Request $request){
           $input=$request->all();
           $search=$input['searchInput'];
           $users = DB::table('users')
           // ->select('name','id')
                ->where('name', 'like',"%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('title', 'like', "%{$search}%")
                ->orWhere('department', 'like', "%{$search}%")
                ->orWhere('telephonenumber', 'like', "%{$search}%")   
                // ->take(10)                                    
                ->get();
            return json_encode($users);
        }

        public function advancedSearchAdUserInModal(Request $request){
            $inputData=$request->all();
            // print_r($inputData);
            
            if(!empty($inputData['email'])){
                $whereData=$inputData['email'];
            }elseif(!empty($inputData['email'])){
                $whereData=$inputData['email'];
            }elseif(!empty($inputData['email'])){
                $whereData=$inputData['email'];
            }elseif(!empty($inputData['title'])){
                $whereData=$inputData['title'];
            }elseif(!empty($inputData['department'])){
                $whereData=$inputData['department'];
            }elseif(!empty($inputData['company'])){
                $whereData=$inputData['company'];
            }elseif(!empty($inputData['phone'])){
                $whereData=$inputData['phone'];
            }
     
            $title=$inputData['title'];
            $department=$inputData['department'];
            $phone=$inputData['phone'];
            $email=$inputData['email'];
            $user_info=DB::table('users');
            // $user_info->where('email','like', "%{$whereData}%");
            // ->orWhere('title', 'like', "%{$title}%")
            // ->orWhere('department', 'like', "%{$department}%")
            // ->orWhere('telephonenumber', 'like', "%{$phone}%")
            // ->take(10)->get();
        
            if(!empty($inputData['email'])){
            $user_info->Where('email', 'like', "%{$email}%");
            }
            if(!empty($inputData['title'])){
            $user_info->Where('title', 'like', "%{$title}%");
            }
            if(!empty($inputData['department'])){
            $user_info->Where('department', 'like', "%{$department}%");
            }
            if(!empty($inputData['phone'])){
            $user_info->Where('telephonenumber', 'like', "%{$phone}%");
            }
            // $user_info->take(10)->get();
            $result=$user_info->get();
            // print_r($result);

            if(!empty($result)){
                     return json_encode($result);

            }else{
                $habib=array();
                return json_encode($habib); 
            }
        


        }


    public function reassign(Request $request){

     $data = [
            'pageTitle' => 'Reassign Request',
            // 'userType'  => Role::orderBy('id', 'DESC')->pluck('name', 'id'),
            'ctrlName'  => 'user',
            'mthdName'  => 'reassign'
        ];
        return view('reassign.create', compact('data'));

        }
    public function searchAssignment(Request $request){
        $userId= $request->input('userId');

        $Initiat=Ticket::where('initiator_id', $userId)->whereNotIn('tStatus',[4,1,5,10])->select('id','tReference_no','tStatus','tSubject')->get();
        $pending=Ticket::where('now_ticket_at', $userId)->whereNotIn('tStatus',[4,1,5,10])->select('id','tReference_no','tStatus','tSubject')->get();
        $circle= DB::table('ticket_approve as TA')
            ->join('tickets', 'TA.ticket_id', '=', 'tickets.id')
            ->where('TA.user_id','=',$userId)
            ->where('TA.action','!=',1)
            // ->OrWhere('tickets.now_ticket_at','=',$userId)
            ->select('tickets.id','tickets.tReference_no','tickets.tStatus','tickets.tSubject')
            ->get();

        $getStatusList=self::getStatusList();
        $data =  ['Initiat'=>$Initiat,'circle'=>$circle,'pending'=>$pending];
        return view('reassign.view_result', compact('data'))->with(['statusResult'=>$getStatusList]);

    }

    public function updateAssignment(Request $request){
       $userId= $request->input('user');
       $ticket_id= $request->input('request_id');
       $requestType= $request->input('requestType');
       $ipAddress=$request->getClientIp();
       $info=Ticket::find($ticket_id);
       if(empty($userId)){
 return '400';
       }
       if($requestType=='Initiat'){

$info->initiator_id=$userId;
// $info->save();
       $insertValu=['user_id'=>Auth::id(),'ip_address'=>$ipAddress,'description'=>"Request Assignment Change By Admin MR . Auth::user()->name and Email Auth::user()->email there is Orginal User $info->initiator_id and Replace user $userId "];


  



       }elseif($requestType=='Pending'){
// $info=Ticket::find($ticket_id);
$statusUpdate=Approve::where('ticket_id','=',$ticket_id)->where('user_id','=',$info->now_ticket_at)->update(['user_id' => $userId]);



 $log=json_decode($info->thistory,true);
$logInfo=array();
  foreach($log as $key => $HistoryInfo){
    if($HistoryInfo['user_id']==$info->now_ticket_at && $HistoryInfo['user_action']==0){
    // $logInfo[]=[
    // 'user_id'=>$HistoryInfo['user_id'],
    // 'user_name'=>$HistoryInfo['user_name'],
    // 'user_type'=>$HistoryInfo['user_type'],
    // 'user_status'=>'Admin Forward',
    // 'user_action'=>1
    // ];

    $logInfo[]=[
                'user_id'=>$HistoryInfo['user_id'],
                'user_name'=>$HistoryInfo['user_name'],
                'designation'=>$HistoryInfo['designation'],
                'user_type'=>$HistoryInfo['user_type'],
                'department'=>$HistoryInfo['department'],
                'company'=>$HistoryInfo['company'],
                'user_status'=>'Admin Forward',
                'user_action'=>1,
                'date'=>date('d-m-Y h:i:s a')
                ];


    $userInfo=User::where('id',$userId)->first();
     // $logInfo[]=[
     // 'user_id'=>$userInfo->id,
     // 'user_name'=>$userInfo->name,
     // 'user_type'=>'Forward',
     // 'user_status'=>'Assign User',
     // 'user_action'=>0,
     // 'date'=>date('d-m-Y H:i:s')
     // ];

    $logInfo[]=[
                'user_id'=> $userInfo->id,
                'user_name'=>$userInfo->name,
                'designation'=>(!empty($userInfo->title)) ? $userInfo->title:'Designation Empty',
                'user_type'=>'Forwarded User',
                'department'=>(!empty($userInfo->department)) ? $userInfo->department:'Department Empty',
                'company'=>(!empty($userInfo->company_name)) ? $userInfo->company_name:'Company Empty',
                'user_status'=>'Pending',
                'user_action'=>0,
                'date'=>date('d-m-Y h:i:s a')
                ];

}else{
 // $logInfo[]=[
 // 'user_id'=>$HistoryInfo['user_id'],
 // 'user_name'=>$HistoryInfo['user_name'],
 // 'user_type'=>$HistoryInfo['user_type'],
 // 'user_status'=>$HistoryInfo['user_status'],
 // 'user_action'=>$HistoryInfo['user_action']
 // ];
   $logInfo[]=[
                'user_id'=>$HistoryInfo['user_id'],
                'user_name'=>$HistoryInfo['user_name'],
                'designation'=>$HistoryInfo['designation'],
                'user_type'=>$HistoryInfo['user_type'],
                'department'=>$HistoryInfo['department'],
                'company'=>$HistoryInfo['company'],
                'user_status'=>$HistoryInfo['user_status'],
                'user_action'=>$HistoryInfo['user_action'],
                'date'=>$HistoryInfo['date']
                ];
}

       }
$info->now_ticket_at=$userId;
$info->tStatus=7;
  $insertValu=['user_id'=>Auth::id(),'ip_address'=>$ipAddress,'description'=>"Request Assignment Change By Admin MR . Auth::user()->name and Email Auth::user()->email there is Orginal User $info->initiator_id and Replace user $userId "];
  $info->thistory=json_encode($logInfo);     

   }



   if($info->save()){
 $activity=DB::table('activity_logs')->insert($insertValu);
 $returnResult='100';
   }else{
     $returnResult='400';
   }
  
return $returnResult;
//                    $udateTicketLog=Ticket::find($inputData['id']);
//  $log=json_decode($udateTicketLog->thistory,true);
// $logInfo=array();
//   foreach($log as $key => $HistoryInfo){
//     if($HistoryInfo['user_id']==Auth::id() && $HistoryInfo['user_action']==0){
//     $logInfo[]=['user_id'=>$HistoryInfo['user_id'],'user_name'=>$HistoryInfo['user_name'],'user_type'=>$HistoryInfo['user_type'],'user_status'=>'Forward','user_action'=>1];
//     $userInfo=User::where('id',$inputData['forwardUser'])->first();
//      $logInfo[]=['user_id'=>$userInfo->id,'user_name'=>$userInfo->name,'user_type'=>'Forward','user_status'=>'Forward User','user_action'=>0];
// }else{
//  $logInfo[]=['user_id'=>$HistoryInfo['user_id'],'user_name'=>$HistoryInfo['user_name'],'user_type'=>$HistoryInfo['user_type'],'user_status'=>$HistoryInfo['user_status'],'user_action'=>$HistoryInfo['user_action']];
// }


//   }

//  $udateTicketLog->thistory=json_encode($logInfo);
// $udateTicketLog->save();


    }


    public function sendMail($TicketID,$Subject,$ticNow){
        $ticNow=$ticNow;
        $TicketID=$TicketID;
         $SubjInfo=Ticket::find($TicketID);
        $Subject=$SubjInfo->tSubject.' From '. Auth::user()->name;
        $id=$TicketID;

        $userInfoMail=User::find($ticNow);
        if(!empty($userInfoMail->email)){
          
            $url='request/details/'.$id;
            $maildata=['URL'=>$url,'name'=>$userInfoMail->name,'subject'=>$Subject,'tReference_no'=>$SubjInfo->tReference_no,'onlySubject'=>$SubjInfo->tSubject];
            Mail::send(['html' => 'emails.mail'], $maildata, function ($message) use ($userInfoMail,$Subject){
            if(empty($userInfoMail->name)){
                $userInfoMailName='';
            }else{
                $userInfoMailName=$userInfoMail->name;
            }
                    $message->to($userInfoMail->email,$userInfoMailName)->subject($Subject);
                    $message->from('ams.noreply@psgbd.com', 'AMS Notification System');
                });

}


    } 

       public function sendApproedMail($TicketID,$Subject,$ticNow){
        $ticNow=$ticNow;
        $TicketID=$TicketID;
         $SubjInfo=Ticket::find($TicketID);
        $Subject=$SubjInfo->tSubject;
        $id=$TicketID;

        $userInfoMail=User::find($ticNow);
        if(!empty($userInfoMail->email)){
          
            $url='request/details/'.$id;
            $maildata=['URL'=>$url,'name'=>$userInfoMail->name,'subject'=>$Subject];
            Mail::send(['html' => 'emails.armail'], $maildata, function ($message) use ($userInfoMail,$Subject){
            if(empty($userInfoMail->name)){
                $userInfoMailName='';
            }else{
                $userInfoMailName=$userInfoMail->name;
            }
                    $message->to($userInfoMail->email,$userInfoMailName)->subject($Subject);
                    $message->from('ams.noreply@psgbd.com', 'AMS Notification System');
                });

}


    }


       public function sendRejectedMail($TicketID,$Subject,$ticNow){
        $ticNow=$ticNow;
        $TicketID=$TicketID;
         $SubjInfo=Ticket::find($TicketID);
        $Subject=$SubjInfo->tSubject;
        $id=$TicketID;

        $userInfoMail=User::find($ticNow);
        if(!empty($userInfoMail->email)){
          
            $url='request/details/'.$id;
            $maildata=['URL'=>$url,'name'=>$userInfoMail->name,'subject'=>$Subject];
            Mail::send(['html' => 'emails.rejectMail'], $maildata, function ($message) use ($userInfoMail,$Subject){
            if(empty($userInfoMail->name)){
                $userInfoMailName='';
            }else{
                $userInfoMailName=$userInfoMail->name;
            }
                    $message->to($userInfoMail->email,$userInfoMailName)->subject($Subject);
                    $message->from('ams.noreply@psgbd.com', 'AMS Notification System');
                });

            }


    }

}


