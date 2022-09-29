<?php

namespace App\Http\Controllers;

use App\SubordinateUser;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Ticket;
use Auth;
use DB;
use Illuminate\Http\Response;
use Illuminate\View\View;
use URL;
use App\Company;
use App\SubCategory;
use App\Category;
use Exception;
use App\User;
use PDF;
use App\TicketApprove as Approve;
use App\TicketHistory;

class ReportController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth');
        // $this->middleware('role:Initiator', ['only' => ['create', 'store', 'ticket_list']]);
    }


    /**
     * @return Factory|Application|View
     */
    public function departmentReport()
    {
        $data = [
            'pageTitle' => 'Department Report Search',
            'mthdName' => 'report',
            'form' => 'department-form'
        ];

        $data = $this->getDepartmentAndCompany($data);

        return view('report.report_master', compact('data'));
    }

    public function getDepartmentAndCompany($data)
    {
        $data['departmentList'] = DB::table('user_categories')
                                ->select('categorys.id as catId', 'categorys.name as name')
                                ->join('categorys','user_categories.category_id','=','categorys.id')
                                ->where('user_categories.user_id', '=' ,Auth::id())
                                ->where('categorys.active_date','<=',date('Y-m-d'))
                                ->where(function ($query) {
                                    $query->whereNull('categorys.deactive_date');
                                    $query->orWhere('categorys.deactive_date', '>=', date('Y-m-d'));
                                })
                                ->orderBy('name')
                                ->pluck('name', 'catId');

        $data['comList'] = Company::where('active_date','<=',date('Y-m-d'))
                            ->where(function ($query) {
                                $query->whereNull('deactive_date');
                                $query->orWhere('deactive_date', '>=', date('Y-m-d'));
                            })
                            ->orderBy('name')
                            ->pluck('name', 'id');

        return $data;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Factory|Application|Response|View
     */
    public function search(Request $request)
    {
        $inputData=$request->all();

        if ($inputData['searchType'] == 1)
        {
            $data = $this->searchFormOne($request);
            return view('report.report_view_table')->with(['result'=> $data['result'], 'statusResult'=> $data['statusResult']]);

        } elseif ($inputData['searchType'] == 2)
        {
            $data = $this->searchFormTwo($request);
            return view('report.report_activite_log_view_table')->with(['result'=> $data['result'], 'statusResult'=> $data['statusResult']]);

        } elseif ($inputData['searchType'] == 3)
        {
            $data = $this->getAuditSearchData($request);
            return view('report.report_view_table')->with(['result'=> $data['result'],'statusResult'=> $data['statusResult']]);

        } elseif($inputData['searchType'] == 4)
        {
            $data = $this->getDepartmentAuditSearchData($request);
            return view('report.report_view_table')->with(['result'=> $data['result'],'statusResult'=> $data['statusResult']]);
        }
    }

    public function getAuditSearchData($request): array
    {
       $inputData = $request->all();

       $ticketInfo = $this->getTicketInfo($inputData);

        if (!empty($inputData['company_id'])) {
            $company_id = $inputData['company_id'];
            $ticketInfo->Where(function ($query) use ($company_id) {
                $query->where('tickets.company_id', '=', $company_id);
            });

        } else {
            $company_list = array();
            if (Auth::user()->user_type == 1) {
                $company_list = Company::where('active_date','<=',date('Y-m-d'))
                    ->Where(function ($query) {
                        $query->whereNull('deactive_date');
                        $query->orWhere('deactive_date', '>=', date('Y-m-d'));
                    })
                    ->orderBy('name')
                    ->pluck('id')
                    ->toArray();

            } elseif (Auth::user()->user_type == 2) {
                $company_list = DB::table('users_company')
                    ->select('company_name.id as id','company_name.name as name')
                    ->join('company_name','users_company.company_id','=','company_name.id')
                    ->where('users_company.user_id', '=' ,Auth::id())
                    ->orderBy('name')
                    ->pluck('id')
                    ->toArray();
            }

            if (!empty($company_list)) {
                $ticketInfo->Where(function ($query) use ($company_list) {
                    $query->whereIn('tickets.company_id', $company_list);
                });
            } else {
                $ticketInfo->Where(function ($query){
                    $query->where('tickets.initiator_id', '=', Auth::id());
                    $query->orWhere('ticket_historys.action_to', '=', Auth::id());
                });
            }
        }

        if (!empty($inputData['initiated_by'])){
            $ticketInfo->where('tickets.initiator_id', '=', $inputData['initiated_by']);
        }

        if (!empty($inputData['approved_by'])){
            $ticketInfo->where('ticket_approve.user_id', '=', $inputData['approved_by']);
        }

        if (!empty($inputData['cat_id'])) {
            $ticketInfo->where('tickets.cat_id','=',$inputData['cat_id']);
        }

        if ($request->has('is_subordinate') && $request->is_subordinate == 'yes'){
            $ticketInfo->whereIn('tickets.initiator_id', SubordinateUser::where('user_id', auth()->id())->pluck('subordinate_user'))
            ->where('tickets.tStatus', '<>', 1);
        }

        $ticketInfo = $this->getQueryData($inputData, $ticketInfo);

        return [
            'result' => $ticketInfo->distinct('tickets.id')->get(),
            'statusResult' => self::getStatusList()
        ];
    }

    public function getDepartmentAuditSearchData($request): array
    {
       $inputData = $request->all();

       $ticketInfo = $this->getTicketInfo($inputData);

        if (!empty($inputData['cat_id'])) {
            $cat_id = $inputData['cat_id'];
            $ticketInfo->where(function ($query) use ($cat_id) {
                $query->where('tickets.cat_id', '=', $cat_id);
            });

        } else {
            $cat_list = DB::table('user_categories')
                ->select('categorys.id as id')
                ->join('categorys','user_categories.category_id','=','categorys.id')
                ->where('user_categories.user_id', '=' ,Auth::id())
                ->pluck('id')
                ->toArray();

            if (!empty($cat_list)) {
                $ticketInfo->where(function ($query) use ($cat_list) {
                    $query->whereIn('tickets.cat_id', $cat_list);
                });
            } else {
                $ticketInfo->where(function ($query){
                    $query->where('tickets.initiator_id', '=', Auth::id());
                    $query->orWhere('ticket_historys.action_to', '=', Auth::id());
                });
            }
        }

        $ticketInfo = $this->getQueryData($inputData, $ticketInfo);

        if (!empty($inputData['company_id'])) {
            $ticketInfo->where('tickets.company_id','=',$inputData['company_id']);
        }

        return [
            'result' => $ticketInfo->distinct('tickets.id')->get(),
            'statusResult' => self::getStatusList()
        ];
    }

    public function getTicketInfo()
    {
        $ticketInfo = DB::table('tickets')
            ->select('tickets.*','users.name as CreatorName','categorys.name as categorysName','sub_categorys.name as sub_categorysName','company_name.name as companyName')
            ->leftJoin('ticket_historys','tickets.id','=','ticket_historys.ticket_id')
            ->leftJoin('ticket_approve','tickets.id','=','ticket_approve.ticket_id')
            ->join('users','tickets.initiator_id','=','users.id')
            ->join('categorys','tickets.cat_id','=','categorys.id')
            ->join('sub_categorys','tickets.sub_cat_id','=','sub_categorys.id')
            ->join('company_name','tickets.company_id','=','company_name.id');

        return $ticketInfo->where('tickets.is_delete', '=', 0);
    }

    public function getQueryData($inputData, $ticketInfo)
    {
        if (!empty($inputData['reference_no'])) {
            $reference_no = $inputData['reference_no'];
            $ticketInfo->where(function ($query) use($reference_no) {
                $query->where('tickets.id', $reference_no);
                $query->orWhere('tickets.tReference_no', 'like', "%$reference_no%");
            });
        }

        if (!empty($inputData['sub_cat_id'])) {
            $ticketInfo->where('tickets.sub_cat_id','=',$inputData['sub_cat_id']);
        }
        if (!empty($inputData['status'])) {
            $ticketInfo->where('tickets.tStatus','=',$inputData['status']);
        }
        if (!empty($inputData['textSerch'])) {
            $textSearch = $inputData['textSerch'];
            $ticketInfo->Where(function ($query) use ($textSearch) {
                $query->where('tickets.tSubject','like',"%{$textSearch}%");
            });
        }

        if (!empty($inputData['start_date'])) {
            $start_date = date('Y-m-d 00:00:00', strtotime($inputData['start_date']));
            if(!empty($inputData['end_date'])){
                $end_date = date('Y-m-d 23:59:59', strtotime($inputData['end_date']));
            }else{
                $end_date = date('Y-m-d').' 23:59:59';
            }
            $ticketInfo->whereBetween('tickets.created_at', [$start_date, $end_date]);
        }

        return $ticketInfo;
    }

    public function searchFormOne($request): array
    {
        $inputData = $request->all();

        $ticketInfo=DB::table('tickets')
            ->select('tickets.*','users.name as CreatorName','categorys.name as categorysName','sub_categorys.name as sub_categorysName','company_name.name as companyName')
            ->join('users','tickets.initiator_id','=','users.id')
            ->join('categorys','tickets.cat_id','=','categorys.id')
            ->join('sub_categorys','tickets.sub_cat_id','=','sub_categorys.id')
            ->join('company_name','tickets.company_id','=','company_name.id')
            ->where('tickets.initiator_id','=',Auth::id());

        $ticketInfo->where('tickets.is_delete', '=', 0);

        if (!empty($inputData['cat_id'])) {
            $ticketInfo->where('tickets.cat_id','=',$inputData['cat_id']);
        }

        if (!empty($inputData['sub_cat_id'])) {
            $ticketInfo->where('tickets.sub_cat_id','=',$inputData['sub_cat_id']);
        }

        if (!empty($inputData['status'])) {
            $ticketInfo->where('tickets.tStatus','=',$inputData['status']);
        }

        if (!empty($inputData['textSerch'])) {
            $textSerch=$inputData['textSerch'];
            $ticketInfo->Where(function ($query) use ($textSerch) {
                $query->where('tickets.tSubject','like',"%{$textSerch}%"); // $query->whereNull('deactive_date');
                $query->orWhere('tickets.tReference_no', 'like',"%{$textSerch}%");
            });
        }

        if (!empty($inputData['start_date'])) {
            $start_date=date('Y-m-d 00:00:00', strtotime($request['start_date']));
            if(!empty($inputData['end_date'])){
                $end_date=date('Y-m-d 23:59:59', strtotime($request['end_date']));
            }else{
                $end_date=date('Y-m-d').' 23:59:59';
            }
            $ticketInfo->whereBetween('tickets.created_at', [$start_date, $end_date]);
        }

        return [
            'result' => $ticketInfo->get(),
            'statusResult' => self::getStatusList(),
        ];
    }

    public function searchFormTwo($request): array
    {
        $inputData = $request->all();

        $ticketInfo=DB::table('ticket_historys')
            ->select('tickets.*','users.name as CreatorName','categorys.name as categorysName','sub_categorys.name as sub_categorysName','ticket_historys.tStatus as userActiviteStatus','company_name.name as companyName')
            ->leftJoin('tickets','ticket_historys.ticket_id','=','tickets.id')
            ->join('users','tickets.initiator_id','=','users.id')
            ->join('categorys','tickets.cat_id','=','categorys.id')
            ->join('sub_categorys','tickets.sub_cat_id','=','sub_categorys.id')
            ->join('company_name','tickets.company_id','=','company_name.id')
            ->where('ticket_historys.action_to','=',Auth::id());

        $ticketInfo->where('tickets.is_delete', '=', 0);

        if (!empty($inputData['cat_id'])) {
            $ticketInfo->where('tickets.cat_id','=',$inputData['cat_id']);
        }

        if (!empty($inputData['sub_cat_id'])) {
            $ticketInfo->where('tickets.sub_cat_id','=',$inputData['sub_cat_id']);
        }

        if (!empty($inputData['status'])) {
            $ticketInfo->where('ticket_historys.tStatus','=',$inputData['status']);
        }
        if (!empty($inputData['textSerch'])) {
            $textSerch=$inputData['textSerch'];
            $ticketInfo->Where(function ($query) use ($textSerch) {
                $query->where('tickets.tSubject','like',"%{$textSerch}%"); // $query->whereNull('deactive_date');
                $query->orWhere('tickets.tReference_no', 'like',"%{$textSerch}%");
            });
        }

        if (!empty($inputData['start_date'])) {
            $start_date=date('Y-m-d 00:00:00', strtotime($request['start_date']));
            if (!empty($inputData['end_date'])) {
                $end_date=date('Y-m-d 23:59:59', strtotime($request['end_date']));
            } else {
                $end_date=date('Y-m-d').' 23:59:59';
            }
            $ticketInfo->whereBetween('ticket_historys.created_at', [$start_date, $end_date]);
        }

        return [
            'result' => $ticketInfo->get(),
            'statusResult' =>self::getStatusList()
        ];
    }

    public function getStatusList(){
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function acknowledgementView($id)
    {

        if(isset($_GET['back'])){
            $PreviousUrl='acknowledgement_list';
        }else{
            $PreviousUrl='report_search';
        }
        $previousUrl=URL::previous();
        // $TicketInfo=Ticket::find($id);
        $TicketInfo=Ticket::where('id', '=', $id)->where('is_delete', '=', 0)->first();


        if( empty($TicketInfo) ){
        	return redirect('/')->with('error', 'Ticket is not available!');
        }

        // dump('new');

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
        if( !in_array(Auth::id(), $ticket_avialable_for_final) && $user->user_type != 1 && $user->user_type != 2){
        	return redirect('/')->with('error', 'You are not authorised to see this ticket!');
        }
        # Check other users details page not show except circle user end.



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




        $attachmentFile=DB::table('tickets_files')->where('ticket_id', '=',$id)->where('is_delete',0)->get();

        $data = [
            'pageTitle' => 'Details View Request',
            'catList'   => Category::orderBy('id', 'DESC')->pluck('name', 'id'),
            'userList'  => User::pluck('users.name', 'users.id'),
            'ctrlName'  => 'ticket',
            'mthdName'  => 'report',
            'TicketInfo'=>$TicketInfo,
            'subcatList'=>$subcatList,
            'PreviousUrl'=>$PreviousUrl,
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

        return view('report.details', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function CreatePDF($id)
    {
        if(isset($_GET['type']) AND $_GET['type']=='L'){
            $pdfType='landscape';

        }else{
            $pdfType='portrait';
        }
        $previousUrl=URL::previous();
        $TicketInfo=Ticket::find($id);
        $subcatList=SubCategory::where('cat_id','=',$TicketInfo->cat_id)->get();
        $recommenderList=DB::table('ticket_approve as TA')->select('US.name','US.id','TA.action')
            ->join('users as US','TA.user_id','=','US.id')
            ->where('TA.user_type', '=',1)
            ->where('TA.ticket_id', '=',$id)
            ->get();
        $approverList=DB::table('ticket_approve as TA')->select('US.name','US.id','TA.action')
            ->join('users as US','TA.user_id','=','US.id')
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



        $comapnyInfo=DB::table('company_name')->where('id','=',$TicketInfo->company_id)->first();
        $category=DB::table('categorys')->where('id','=',$TicketInfo->cat_id)->first();

        // dd($TicketInfo);

        $data = [
            'pageTitle' => 'Details View Request',
            'TicketInfo'=>$TicketInfo,
            'approverList'=>$approverList,
            'recommenderList'=>$recommenderList,
            'comapnyInfo'=>$comapnyInfo,
            'category'=>$category,
            'PreviousComment' => $PreviousComment,
        ];


        // PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif','isHtml5ParserEnabled'=> TRUE]);

        if(isset($_GET['type']) AND $_GET['type']=='L'){
        	$orientation = 'L';
        }
        else{
        	$orientation = 'P';
        }



        $pdf = PDF::loadView('report.pdf', $data, [], [
        	'mode' => 'utf-8',
    	    // 'format' => [190, 236],
        	'format' => 'A4',
        	'orientation' => $orientation,
        	'allow_charset_conversion' => true,
        	'charset_in' => 'iso-8859-4',
        	// 'autoPageBreak' => true,
        	// 'autoMarginPadding' => 40,
        	// 'setAutoTopMargin' => true,
        	//'mirrorMargins' => 30,
        	//'scale' => 0.8,
	        //'height' => 500,
//	        'pagenumPrefix' => 'Page number ',
//            'pagenumSuffix' => ' - ',
//            'nbpgPrefix' => ' out of ',
//            'nbpgSuffix' => ' pages',
//            'aliasNbPgGp' => "{nb}",
            "show_watermark" => true,
            // "watermark" => "wwww",
        ]);





        // $html =

        // $pdf->WriteHTML($TicketInfo->tDescription, PDF::HTML_BODY, false, false);

        // $pdf->allow_charset_conversion = true;
        // $pdf->charset_in = 'iso-8859-4';



        // $pdf->setFooter('{PAGENO}{nbpg}');
        // PDF::SetHTMLFooter("Page {PAGENO} of {nb}");

        //$pdf_string = $pdf->output('', 'S');

        // dd(json_decode($TicketInfo->thistory));

        // $pdf->getMpdf()->DefHTMLHeaderByName(
        //   'Chapter2Header',
        //   '<div style="text-align: right; border-bottom: 1px solid #000000;
        //   font-weight: bold; font-size: 10pt;">Chapter 2</div>'
        // );

        // $pdf->getMpdf()->DefHTMLFooterByName(
        //   'Chapter2Footer',
        //   '<div style="text-align: right; font-weight: bold; font-size: 8pt;
        //   font-style: italic;">Chapter 2 Footer</div>'
        // );

        // $pdf->getMpdf()->SetWatermarkText('Partex', 0.2, 'F', 'F');
        // $pdf->getMpdf()->showWatermarkText = true;


        if(!empty($PreviousComment)){


            $html_page_two = '<div id="page_wrapper">';

            $html_page_two .= '<h3 style="padding-bottom: 5px;margin-bottom:5px;">Previous Comment</h3>';

            $html_page_two .= '<table class="thistory">
								<tr style="background-color: #c8cace;">
									<th>Sl</th>
									<th>Name</th>
									<th>Comment</th>
									<th>Date</th>
								</tr>
                                <tbody>';
           $i = 1;
            foreach($PreviousComment as $comment){
            $html_page_two .= '<tr><td class="tbltb">
                '.$i++.'
            </td>';
            $html_page_two .= '<td class="tbltb">
            '.$comment->User_name.'
            </td>';
             $html_page_two .= '<td class="tbltb">
             '.strip_tags($comment->tDescription).'
            </td>';
            $html_page_two .= '<td class="tbltb">
            '.date('d-M-Y H:i:s', strtotime($comment->created_at)).'
           </td>';

           $html_page_two .= '</tr>';
            }

           $html_page_two .= '</tbody>
                            </table></div>';



            $pdf->getMpdf()->WriteHTML($html_page_two);
        }


        if( !empty($TicketInfo->thistory) ){

        	$html_page_two = '<div id="page_wrapper">';

        	$html_page_two .= '<h3 style="padding-bottom: 5px;margin-bottom:5px;">Approval Sequence</h3>';

        	$ticket_historys = json_decode($TicketInfo->thistory,true);

        	$html_page_two .= '<table class="thistory">
								<tr style="background-color: #c8cace;">
									<th>Name</th>
									<th>Designation</th>
									<th>Type</th>
									<th>Department</th>
									<th>Company</th>
									<th>Status</th>
									<th>Date</th>
								</tr>
								<tbody>';


			foreach ($ticket_historys as $HistoryInfo) {

				$user_info = User::find($HistoryInfo['user_id']);

				$html_page_two .= '<tr>
						              	<td class="tbltb">'.$HistoryInfo["user_name"].'</td>';

						              	if( isset($user_info->title) && !empty($user_info->title) ){
						              		$html_page_two .= '<td class="tbltb">'.$user_info->title.'</td>';
						              	}else{
						              		$html_page_two .= '<td class="tbltb">Not Found</td>';
						              	}

	              	$html_page_two .= '<td class="tbltb">'.$HistoryInfo["user_type"].'</td>';

	              						if( isset($user_info->department) && !empty($user_info->department) ){
	              							$html_page_two .= '<td class="tbltb">'.$user_info->department.'</td>';
	              						}else{
	              							$html_page_two .= '<td class="tbltb">Not Found</td>';
	              						}

	              						if( isset($user_info->company_name) && !empty($user_info->company_name) ){
	              							$html_page_two .= '<td class="tbltb">'.$user_info->company_name.'</td>';
	              						}else{
	              							$html_page_two .= '<td class="tbltb">Not Found</td>';
	              						}

	              	$html_page_two .= '<td class="tbltb">'.$HistoryInfo["user_status"].'</td>
						              	<td class="tbltb">';

							              	if(isset($HistoryInfo["date"]) && !empty($HistoryInfo["date"])){
							             		$html_page_two .= date('d-M-Y H:i:s', strtotime($HistoryInfo["date"]));
							              	}

					$html_page_two .= '</td>
						          	</tr>';

			} // End foreach

			$html_page_two .= '</tbody>
                            </table></div>';


			$pdf->getMpdf()->WriteHTML($html_page_two);


        }



        // Ticket history



        // $dom_pdf = $pdf->getDomPDF();
        // $canvas = $dom_pdf ->get_canvas();
        // $footer = $canvas->open_object();
        // $w = $canvas->get_width();`
        // $h = $canvas->get_height();
        // $canvas->page_text($w-810,$h-58,"", null, 10, array(0, 0, 0));
        // if(isset($_GET['type']) AND $_GET['type']=='L'){
        //     $canvas->page_text($w-810,$h-49,"Computer Generated Approval Note. No Signature Required", null, 11, array(10, 0, 0));
        //     $canvas->page_text($w-80,$h-35,"Page {PAGE_NUM}  Of  {PAGE_COUNT}",null, 10, array(0, 0, 0));
        // //     $canvas->page_text($w-810,$h-19,"Approval Management System                                                                                                                                                                            Powered By:PSG-IT|copyright ©2019 Partex Star Group ", null, 9, array(1, 0, 0));
        // }else{
        //     $canvas->page_text($w-580,$h-49,"Computer Generated Approval Note. No Signature Required", null, 11, array(10, 0, 0));
        //     $canvas->page_text($w-80,$h-35,"Page {PAGE_NUM}  Of  {PAGE_COUNT}",null, 10, array(0, 0, 0));
        //     // $canvas->page_text($w-580,$h-19,"Approval Management System                                                                                   Powered By:PSG-IT|copyright ©2019 Partex Star Group ", null, 9, array(1, 0, 0));

        // }
        // $canvas->close_object();
        // $canvas->add_object($footer,"all");

        if(empty($TicketInfo->tReference_no)){
            $name='report';
        }else{
            $name=$TicketInfo->tReference_no;
        }
        return $pdf->stream($name.'.pdf');



    }




    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function acknowledgement_list(Request $request)
    {
        $data = [
            'pageTitle' => 'Acknowledgement List',
            'userList'  => User::pluck('users.name', 'users.id'),
            'listData'  =>DB::table('ticket_historys as TH')
                ->leftJoin('tickets as t', 'TH.ticket_id', '=', 't.id')
                ->leftJoin('users as u', 'u.id', '=', 'TH.action_to')
                ->leftJoin('categorys as c', 'c.id', '=', 't.cat_id')
                ->leftJoin('sub_categorys as sc', 'sc.id', '=', 't.sub_cat_id')
                ->select('t.id', 't.tReference_no', 't.tSubject', 'u.name AS user_name', 'c.name AS cat_name', 'sc.name AS sub_cat_name','t.tStatus','t.initiator_id','t.now_ticket_at','t.thistory')
                ->where('TH.action_to', '=',Auth::id())
                ->where('TH.tStatus', '=',9)->get(),
            'mthdName'  => 'report'
        ];
        $getStatusList=Self::getStatusList();
        return view('report.acknowledgement_list', compact('data'))->with(['StatusList'=>$getStatusList]);
    }
}
