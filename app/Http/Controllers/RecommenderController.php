<?php

namespace App\Http\Controllers;

use App\Ticket;
use App\TicketHistory;
use App\Category;
use App\SubCategory;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTicket;
use Exception;
use Auth;
use URL;

class RecommenderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       // $tStatus = $status;//$request->route()->getAction()['tStatus'];
       //  if($tStatus == 2){
       //      $pageTitle = 'Pending Requests';
       //      $mthdName  = 'pending';

       //  }elseif($tStatus == 1){
       //      $pageTitle = 'Draft Requests';
       //      $mthdName  = 'draft';

       //  }elseif($tStatus == 6){
       //      $pageTitle = 'Requests for Information';
       //      $mthdName  = 'request_info';

       //  }elseif($tStatus == 5){
       //      $pageTitle = 'Rejected Requests';
       //      $mthdName  = 'rejected';

       //  }else{
       //      $pageTitle = 'Approved Requests';
       //      $mthdName  = 'approved';
       //  }
        // 

    $pageTitle = 'Index Requests';
    $mthdName  = 'Index';
        $data = [
            'pageTitle' => $pageTitle,
            'listData'  => Ticket::from( 'tickets as t' )
                                ->join('users as u', 'u.id', '=', 't.recommender_id')
                                ->join('categorys as c', 'c.id', '=', 't.cat_id')
                                ->join('sub_categorys as sc', 'sc.id', '=', 't.sub_cat_id')
                                ->where('t.recommender_id', '=', $request->session()->get('userID'))
                                // ->where('t.tStatus', '=', $tStatus)
                                ->orderBy('t.id', 'DESC')
                                ->paginate(10, ['t.id', 't.tReference_no', 't.tSubject', 'u.name AS user_name', 'c.name AS cat_name', 'sc.name AS sub_cat_name','t.tStatus']),
            'ctrlName'  => 'ticket',
            'SubctrlName'  => 'recommenderIndex',
            'mthdName'  => $mthdName,
            'status'=>self::StatusArry()
        ];
        return view('recommender.list', compact('data'));
    }

    public function StatusArry(){
        $status=[
            '1'=>'Draft by Initiator',
            '2'=>'Submit by Initiator',
            '3'=>'Draft by Approver',
            '4'=>'Approved',
            '5'=>'Rejected',
            '6'=>'Request for Info',
            '7'=>'Forward',
            '8'=>'Forward(After Reject)',
            '9'=>'Forward(After Approved)',
            '10'=>'Disable'
    ];
    return $status;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function recommender_ticket_list(Request $request,$status)
    {

         $tStatus = $status;//$request->route()->getAction()['tStatus'];
        if($tStatus == 2){
            $pageTitle = 'Pending Requests';
            $SubctrlName = 'Pending Requests';
            $mthdName  = 'pending';

        }elseif($tStatus == 1){
            $pageTitle = 'Draft Requests';
            $SubctrlName='Draft Requests';
            $mthdName  = 'draft';

        }elseif($tStatus == 6){
            $pageTitle = 'Requests for Information';
            $SubctrlName = 'Requests for Information';
            $mthdName  = 'request_info';

        }elseif($tStatus == 5){
            $pageTitle = 'Rejected Requests';
            $SubctrlName = 'Rejected Requests';
            $mthdName  = 'rejected';

        }
elseif($tStatus == 7){
            $pageTitle = 'Forward Requests';
            $SubctrlName = 'Forward Requests';
            $mthdName  = 'forward';

        }

        else{
            $pageTitle = 'Approved Requests';
            $SubctrlName = 'Approved Requests';
            $mthdName  = 'approved';
        }

    //         $pageTitle = 'Index Requests';
    // $mthdName  = 'Index';
        $data = [
            'pageTitle' => $pageTitle,
            'listData'  => Ticket::from( 'tickets as t' )
                                ->join('users as u', 'u.id', '=', 't.recommender_id')
                                ->join('categorys as c', 'c.id', '=', 't.cat_id')
                                ->join('sub_categorys as sc', 'sc.id', '=', 't.sub_cat_id')
                                ->where('t.recommender_id', '=', $request->session()->get('userID'))
                                ->where('t.tStatus', '=', $tStatus)
                                ->orderBy('t.id', 'DESC')
                                ->paginate(10, ['t.id', 't.tReference_no', 't.tSubject', 'u.name AS user_name', 'c.name AS cat_name', 'sc.name AS sub_cat_name','t.tStatus']),
            'ctrlName'  => 'ticket',
            'SubctrlName'  => $SubctrlName,
            'mthdName'  => $mthdName,
            'status'=>self::StatusArry()
        ];
        return view('recommender.list', compact('data'));
    
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {


       $previousUrl=URL::previous();
       //  return redirect($previousUrl);
       // exit();
       $TicketInfo=Ticket::find($id);
       $subcatList=SubCategory::where('cat_id','=',$TicketInfo->cat_id)->get();
       $data = [
            'pageTitle' => 'Details View Request',
            'catList'   => Category::orderBy('id', 'DESC')->pluck('name', 'id'),
            'userList'  => User::join('role_user as ru', 'ru.user_id', '=', 'users.id')
                                ->join('roles as r', 'r.id', '=', 'ru.role_id')
                                ->where('r.name', '=', 'Recommender')
                                ->pluck('users.name', 'users.id'),
            'ctrlName'  => 'ticket',
            'SubctrlName'  => 'recommenderIndex',
       
            'TicketInfo'=>$TicketInfo,
            'subcatList'=>$subcatList,
            'previousUrl'=>$previousUrl
        ];

 return view('recommender.details', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
