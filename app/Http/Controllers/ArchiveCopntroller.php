<?php

namespace App\Http\Controllers;

use App\Category;
use App\Company;
use App\SubCategory;
use App\Ticket;
use App\Traits\AuditLogTrait;
use App\User;
use Auth;
use DB;
use Exception;
use Illuminate\Http\Request;
use PDF;
use URL;

class ArchiveCopntroller extends Controller
{
    use AuditLogTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageTitle = 'Live Archive';
        $data      = [
            'pageTitle' => $pageTitle,
            'listData'  => Ticket::from('tickets as t')
                ->join('users as u', 'u.id', '=', 't.now_ticket_at')
                ->join('categorys as c', 'c.id', '=', 't.cat_id')
                ->join('sub_categorys as sc', 'sc.id', '=', 't.sub_cat_id')
            // ->where('t.initiator_id', '=', $request->session()->get('userID'))
                ->where('t.tStatus', '=', 4)
                ->where('t.initiator_id', '=', Auth::id())
                ->where('t.is_delete', '=', 0)
            // ->orWhere('t.now_ticket_at','=',Auth::id())

                ->orderBy('t.id', 'DESC')
                ->paginate(10, ['t.id', 't.tReference_no', 't.tSubject', 'u.name AS user_name', 'c.name AS cat_name', 'sc.name AS sub_cat_name', 't.tStatus', 't.initiator_id', 't.now_ticket_at', 't.thistory']),
            'catList'   => Category::where('active_date', '<=', date('Y-m-d'))
                ->Where(function ($query) {
                    $query->whereNull('deactive_date');
                    $query->orWhere('deactive_date', '>=', date('Y-m-d'));})
                ->orderBy('id', 'DESC')
                ->pluck('name', 'id'),
            'ctrlName'  => 'Archive',
            'mthdName'  => 'LocalArchive',
        ];
        $getStatusList = Self::getStatusList();
        // print_r($getStatusList);
        // exit();

        return view('archive.live.archive_list', compact('data'))->with(['StatusList' => $getStatusList]);

    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function archive($id)
    {
        $DB           = DB::select("call  InsertTicketInArcTicket($id)");
        $redirect_msg = 'Archive successfull';

        $ticket = Ticket::select(['tReference_no','tSubject'])->findorfail($id);
        $this->logStore('created','archive',"$ticket->tSubject( $ticket->tReference_no ) ticket archived.",'archive live');

        return redirect()->back()->with('status', $redirect_msg);

    }

    public function save_archive(Request $request)
    {
        try {
            $inputData = $request->all();
            $inputList = $inputData['arc_id'];

            if (count($inputList) > 0) {
                foreach ($inputList as $key => $value) {
                    // echo $value;
                    DB::select("call  InsertTicketInArcTicket($value)");

                    $ticket = Ticket::select(['tReference_no','tSubject'])->findorfail($value);
                    $this->logStore('created','archive',"$ticket->tSubject( $ticket->tReference_no ) ticket archived.",'archive live');
                }

            }

            $redirect_msg = 'Archive successfull';
            return redirect()->back()->with('status', $redirect_msg);
        } catch (Exception $e) {
            return back()->withError('Something is wrong.');
            // return back()->withError($e->getCode().' : '.$e->getMessage())>with('status', $redirect_msg);;
        }

    }

    public function archiveBack($id)
    {
        $DB           = DB::select("call  backtrackFormArchive($id)");
        $redirect_msg = 'Archive Back successfull';
        return redirect()->back()->with('status', $redirect_msg);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function archive_list()
    {
        $pageTitle = 'Archive';
        $data      = [
            'pageTitle' => $pageTitle,
            'listData'  => Ticket::from('arc_ticket as t')
                ->join('users as u', 'u.id', '=', 't.now_ticket_at')
                ->join('categorys as c', 'c.id', '=', 't.cat_id')
                ->join('sub_categorys as sc', 'sc.id', '=', 't.sub_cat_id')
            // ->where('t.initiator_id', '=', $request->session()->get('userID'))
                ->where('t.tStatus', '=', 4)
                ->where('t.initiator_id', '=', Auth::id())
            // ->orWhere('t.now_ticket_at','=',Auth::id())

                ->orderBy('t.id', 'DESC')
                ->paginate(10, ['t.id', 't.tReference_no', 't.tSubject', 'u.name AS user_name', 'c.name AS cat_name', 'sc.name AS sub_cat_name', 't.tStatus', 't.initiator_id', 't.now_ticket_at', 't.thistory']),
            'ctrlName'  => 'Archive',
            'mthdName'  => 'Archive',
        ];
        $getStatusList = Self::getStatusList();
        // print_r($getStatusList);
        // exit();

        return view('archive.arc_archive_list', compact('data'))->with(['StatusList' => $getStatusList]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function archiveAcknowledgement($id)
    {
        if (isset($_GET['back'])) {
            $PreviousUrl = 'acknowledgement_list';
        } else {
            $PreviousUrl = 'archive/archive_search';
        }
        $previousUrl     = URL::previous();
        $TicketInfo      = DB::table('arc_ticket')->where('id', '=', $id)->where('is_delete', '=', 0)->first(); //Ticket::find($id);

        if( empty($TicketInfo) ){
        	return redirect('/')->with('error', 'Ticket is not available!');
        }

        # Check other users details page not show except circle user.
        $ticket_avialable_for = [];
        # approver and recommender
        $approver_recommender = DB::table('arc_ticket_approve')->where('ticket_id', '=', $TicketInfo->id)->get(['user_id'])->toArray();
        $ticket_avialable_for = array_map(function($values){
        	$array_values = (array)($values);
        	return $array_values;
        }, $approver_recommender);

        # get history users
        $history_users = DB::table('arc_ticket_historys')->where('ticket_id', '=', $TicketInfo->id)->get(['created_by']);
        if( !empty($history_users) ){

        	$history_users = array_map(function($values){
        		$array_values = (array)($values);
        		return $array_values;
        	}, $history_users->toArray());

        	$ticket_avialable_for = array_merge($ticket_avialable_for, $history_users);
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
        	return redirect('/')->with('error', 'You are not authorised to see this ticket!');
        }
        # Check other users details page not show except circle user end.



        $subcatList      = SubCategory::where('cat_id', '=', $TicketInfo->cat_id)->get();
        $recommenderList = User::select('users.name', 'users.id')
            ->join('arc_ticket_approve as TA', 'TA.user_id', '=', 'users.id')
            ->where('TA.user_type', '=', 1)
            ->where('TA.ticket_id', '=', $id)
            ->get();
        $approverList = User::select('users.name', 'users.id')
            ->join('arc_ticket_approve as TA', 'TA.user_id', '=', 'users.id')
            ->where('TA.user_type', '=', 2)
            ->where('TA.ticket_id', '=', $id)
            ->get();
        // print_r($recommenderList);

        $PreviousComment = DB::table('arc_ticket_historys as TH')
            ->join('users as UI', 'TH.action_to', '=', 'UI.id')
            ->select('TH.*', 'UI.name as User_name')
            ->where('TH.ticket_id', '=', $id)
            ->orderBy('TH.id', 'asc')
            ->get();

        $attachmentFile = DB::table('arc_tickets_files')->where('ticket_id', '=', $id)->where('is_delete', 0)->get();

        $data = [
            'pageTitle'       => 'Details View Request Archive',
            'catList'         => Category::orderBy('id', 'DESC')->pluck('name', 'id'),
            'userList'        => User::pluck('users.name', 'users.id'),
            'ctrlName'        => 'Archive',
            'mthdName'        => 'ArchiveReport',
            'TicketInfo'      => $TicketInfo,
            'subcatList'      => $subcatList,
            'PreviousUrl'     => $PreviousUrl,
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

        return view('archive.report.details', compact('data'));
    }

    public function archiveSearch(Request $request)
    {
        $pageTitle = 'Archive Search';
        $data      = [
            'pageTitle' => $pageTitle,
            'userList'  => User::pluck('users.name', 'users.id'),
            'catList'   => Category::where('active_date', '<=', date('Y-m-d'))
                ->Where(function ($query) {
                    $query->whereNull('deactive_date');
                    $query->orWhere('deactive_date', '>=', date('Y-m-d'));})
                ->orderBy('id', 'DESC')
                ->pluck('name', 'id'),
            'ctrlName'  => 'Archive',
            'mthdName'  => 'ArchiveReport',
        ];

        return view('archive.report.report_master', compact('data'));
    }

    /**
     * archiveReportSearch the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function archiveReportSearch(Request $request)
    {
        $inputData = $request->all();

        $textSerch  = $inputData['textSerch'];
        $ticketInfo = DB::table('arc_ticket')
            ->select('arc_ticket.*', 'users.name as CreatorName', 'categorys.name as categorysName', 'sub_categorys.name as sub_categorysName')
            ->join('users', 'arc_ticket.initiator_id', '=', 'users.id')
            ->join('categorys', 'arc_ticket.cat_id', '=', 'categorys.id')
            ->join('sub_categorys', 'arc_ticket.sub_cat_id', '=', 'sub_categorys.id');

	        $ticketInfo->where('arc_ticket.is_delete', '=', 0);

        if (!empty($inputData['cat_id'])) {
            $ticketInfo->where('arc_ticket.cat_id', '=', $inputData['cat_id']);
        }
        if (!empty($inputData['sub_cat_id'])) {
            $ticketInfo->where('arc_ticket.sub_cat_id', '=', $inputData['sub_cat_id']);
        }
        if (Auth::user()->user_type !== 1) {
            $ticketInfo->where('arc_ticket.initiator_id', '=', Auth::id());
        }

        if (!empty($inputData['textSerch'])) {
            $ticketInfo->Where(function ($query) use ($textSerch) {
                $query->where('arc_ticket.tSubject', 'like', "%{$textSerch}%"); // $query->whereNull('deactive_date');
                $query->orWhere('arc_ticket.tReference_no', 'like', "%{$textSerch}%");
            });
        }

        if (!empty($inputData['start_date'])) {
            $start_date = date('Y-m-d 00:00:00', strtotime($request['start_date']));
            if (!empty($inputData['end_date'])) {
                $end_date = date('Y-m-d 23:59:59', strtotime($request['end_date']));
            } else {
                $end_date = date('Y-m-d') . ' 23:59:59';
            }
            $ticketInfo->whereBetween('arc_ticket.created_at', [$start_date, $end_date]);
        }
        $result = $ticketInfo->get();

        $statusResult = self::getStatusList();

        return view('archive.report.report_view_table')->with(['result' => $result, 'statusResult' => $statusResult]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function archivePDF($id)
    {
        if (isset($_GET['type']) and $_GET['type'] == 'L') {
            $pdfType = 'landscape';

        } else {
            $pdfType = 'portrait';
        }

        $previousUrl = URL::previous();
        $TicketInfo  = DB::table('arc_ticket')->where('id', '=', $id)->first();

        $subcatList      = SubCategory::where('cat_id', '=', $TicketInfo->cat_id)->get();
        $recommenderList = DB::table('arc_ticket_approve as TA')->select('US.name', 'US.id', 'TA.action')
            ->join('users as US', 'TA.user_id', '=', 'US.id')
            ->where('TA.user_type', '=', 1)
            ->where('TA.ticket_id', '=', $id)
            ->get();
        $approverList = DB::table('arc_ticket_approve as TA')->select('US.name', 'US.id', 'TA.action')
            ->join('users as US', 'TA.user_id', '=', 'US.id')
            ->where('TA.user_type', '=', 2)
            ->where('TA.ticket_id', '=', $id)
            ->get();

        $PreviousComment = DB::table('arc_ticket_historys as TH')
            ->join('users as UI', 'TH.action_to', '=', 'UI.id')
            ->select('TH.*', 'UI.name as User_name')
            ->where('TH.ticket_id', '=', $id)
            ->orderBy('TH.id', 'asc')
            ->get();

        $comapnyInfo = DB::table('company_name')->where('id', '=', $TicketInfo->company_id)->first();
        $category    = DB::table('categorys')->where('id', '=', $TicketInfo->cat_id)->first();

        $data = [
            'pageTitle'       => 'Details View Request',
            'TicketInfo'      => $TicketInfo,
            'approverList'    => $approverList,
            'recommenderList' => $recommenderList,
            'comapnyInfo'     => $comapnyInfo,
            'category'        => $category,
            'PreviousComment'=> $PreviousComment,
        ];

        # new pdf
        if (isset($_GET['type']) and $_GET['type'] == 'L') {
            $orientation = 'L';
        } else {
            $orientation = 'P';
        }

        $pdf = PDF::loadView('report.pdf', $data, [], [
            'mode'                     => 'utf-8',
            // 'format' => [190, 236],
            'format'                   => 'A4',
            'orientation'              => $orientation,
            'allow_charset_conversion' => true,
            'charset_in'               => 'iso-8859-4',
            // 'autoPageBreak' => true,
            // 'autoMarginPadding' => 40,
            // 'setAutoTopMargin' => true,
            //'mirrorMargins' => 30,
            //'scale' => 0.8,
            //'height' => 500,
            'pagenumPrefix'            => 'Page number ',
            'pagenumSuffix'            => ' - ',
            'nbpgPrefix'               => ' out of ',
            'nbpgSuffix'               => ' pages',
            'aliasNbPgGp'              => "{nb}",
            "show_watermark" 			=> true,
        ]);

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

        if (!empty($TicketInfo->thistory)) {


            $html_page_two = '<div id="page_wrapper">';

            $html_page_two .= '<h3 style="padding-bottom: 10px;">Approval Sequence</h3>';

            $ticket_historys = json_decode($TicketInfo->thistory, true);

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
						              	<td class="tbltb">' . $HistoryInfo["user_name"] . '</td>';

                if (isset($user_info->title) && !empty($user_info->title)) {
                    $html_page_two .= '<td class="tbltb">' . $user_info->title . '</td>';
                } else {
                    $html_page_two .= '<td class="tbltb">No Designation</td>';
                }

                $html_page_two .= '<td class="tbltb">' . $HistoryInfo["user_type"] . '</td>';

                if (isset($user_info->department) && !empty($user_info->department)) {
                    $html_page_two .= '<td class="tbltb">' . $user_info->department . '</td>';
                } else {
                    $html_page_two .= '<td class="tbltb">No Department</td>';
                }

                if (isset($user_info->company_name) && !empty($user_info->company_name)) {
                    $html_page_two .= '<td class="tbltb">' . $user_info->company_name . '</td>';
                } else {
                    $html_page_two .= '<td class="tbltb">No Company</td>';
                }

                $html_page_two .= '<td class="tbltb">' . $HistoryInfo["user_status"] . '</td>
						              	<td class="tbltb">';

                if (isset($HistoryInfo["date"]) && !empty($HistoryInfo["date"])) {
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

        //   $pdf = PDF::loadView('report.pdf', $data)->setPaper('A4', $pdfType);
        //   $pdf->output();
        //   $dom_pdf = $pdf->getDomPDF();
        //   $canvas = $dom_pdf ->get_canvas();
        //   $footer = $canvas->open_object();
        //   $w = $canvas->get_width();
        //   $h = $canvas->get_height();
        //   $canvas->page_text($w-810,$h-58,"", null, 10, array(0, 0, 0));
        //        if(isset($_GET['type']) AND $_GET['type']=='L'){
        //   $canvas->page_text($w-810,$h-49,"Computer Generated Approval Note. No Signature Required", null, 11, array(10, 0, 0));
        //   $canvas->page_text($w-80,$h-35,"Page {PAGE_NUM}  Of  {PAGE_COUNT}",null, 10, array(0, 0, 0));
        //   // $canvas->page_text($w-810,$h-19,"Approval Management System                                                                                                                                                                            Powered By:PSG-IT|copyright ©2019 Partex Star Group ", null, 9, array(1, 0, 0));
        // }else{
        //  $canvas->page_text($w-580,$h-49,"Computer Generated Approval Note. No Signature Required", null, 11, array(10, 0, 0));
        //   $canvas->page_text($w-80,$h-35,"Page {PAGE_NUM}  Of  {PAGE_COUNT}",null, 10, array(0, 0, 0));
        //   // $canvas->page_text($w-580,$h-19,"Approval Management System                                                                                   Powered By:PSG-IT|copyright ©2019 Partex Star Group ", null, 9, array(1, 0, 0));

        // }

//     $pdf->output();
        //     $dom_pdf = $pdf->getDomPDF();
        //     $dom_pdf->setPaper('A4', 'portrait');
        // $canvas = $dom_pdf ->get_canvas();
        // $footer = $canvas->open_object();
        // $w = $canvas->get_width();
        // $h = $canvas->get_height();
        // $canvas->page_text($w-580,$h-58,"", null, 10, array(0, 0, 0));
        // $canvas->page_text($w-580,$h-49,"Computer Generate Approval Note. No Signature Required", null, 11, array(10, 0, 0));
        // $canvas->page_text($w-80,$h-35,"Page {PAGE_NUM}  Of  {PAGE_COUNT}",null, 10, array(0, 0, 0));
        // $canvas->page_text($w-580,$h-19,"Approval Management System                                                                                                    Powered By:PSG-IT|copyright ©2019 Partex Star Group ", null, 9, array(1, 0, 0));
        // $canvas->close_object();
        // $canvas->add_object($footer,"all");

        if (empty($TicketInfo->tReference_no)) {
            $name = 'report';
        } else {
            $name = $TicketInfo->tReference_no;
        }
        return $pdf->stream($name . '.pdf');
    }

    public function getStatusList()
    {
        $statusList[1]  = 'Save as Draft ';
        $statusList[2]  = 'Pending';
        $statusList[3]  = 'Draft by Approver ';
        $statusList[4]  = 'Approved ';
        $statusList[5]  = 'Rejected ';
        $statusList[6]  = 'Request for Info ';
        $statusList[7]  = 'Forward';
        $statusList[8]  = 'Appoved And Forward';
        $statusList[9]  = 'Appoved And Acknowledgement';
        $statusList[10] = 'Disable';
        $statusList[11] = 'Pending';
        return $statusList;
    }

    /**
     * archiveReportSearch the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function liveReportSearch(Request $request)
    {

        $inputData  = $request->all();
        $textSerch  = $inputData['textSerch'];
        $ticketInfo = DB::table('tickets')
            ->select('tickets.*', 'users.name as CreatorName', 'categorys.name as categorysName', 'sub_categorys.name as sub_categorysName')
            ->join('users', 'tickets.initiator_id', '=', 'users.id')
            ->join('categorys', 'tickets.cat_id', '=', 'categorys.id')
            ->join('sub_categorys', 'tickets.sub_cat_id', '=', 'sub_categorys.id');
        if (!empty($inputData['cat_id'])) {
            $ticketInfo->where('tickets.cat_id', '=', $inputData['cat_id']);
        }
        if (!empty($inputData['sub_cat_id'])) {
            $ticketInfo->where('tickets.sub_cat_id', '=', $inputData['sub_cat_id']);
        }
        // if(Auth::user()->user_type!==1){
        $ticketInfo->where('tickets.initiator_id', '=', Auth::id());
        $ticketInfo->where('tickets.tStatus', '=', 4);
        // }
        $ticketInfo->where('tickets.is_delete', '=', 0);

        if (!empty($inputData['textSerch'])) {
            $ticketInfo->Where(function ($query) use ($textSerch) {
                $query->where('tickets.tSubject', 'like', "%{$textSerch}%"); // $query->whereNull('deactive_date');
                $query->orWhere('tickets.tReference_no', 'like', "%{$textSerch}%");
            });
        }

        if (!empty($inputData['start_date'])) {
            $start_date = date('Y-m-d 00:00:00', strtotime($request['start_date']));
            if (!empty($inputData['end_date'])) {
                $end_date = date('Y-m-d 23:59:59', strtotime($request['end_date']));
            } else {
                $end_date = date('Y-m-d') . ' 23:59:59';
            }
            $ticketInfo->whereBetween('tickets.created_at', [$start_date, $end_date]);
        }
        $result = $ticketInfo->get();

        $statusResult = self::getStatusList();

        return view('archive.live.report_view_table')->with(['result' => $result, 'statusResult' => $statusResult]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function liveAcknowledgement($id)
    {
        if (isset($_GET['back'])) {
            $PreviousUrl = 'acknowledgement_list';
        } else {
            $PreviousUrl = 'archive/index';
        }
        $previousUrl     = URL::previous();
        $TicketInfo      = DB::table('tickets')->where('id', '=', $id)->where('is_delete', '=', 0)->first(); //Ticket::find($id);

        if( empty($TicketInfo) ){
        	return redirect('/')->with('error', 'Ticket is not available!');
        }

        # Check other users details page not show except circle user.
        $ticket_avialable_for = [];
        # approver and recommender
        $approver_recommender = DB::table('ticket_approve')->where('ticket_id', '=', $TicketInfo->id)->get(['user_id'])->toArray();
        $ticket_avialable_for = array_map(function($values){
        	$array_values = (array)($values);
        	return $array_values;
        }, $approver_recommender);

        # get history users
        $history_users = DB::table('ticket_historys')->where('ticket_id', '=', $TicketInfo->id)->get(['created_by']);
        if( !empty($history_users) ){

        	$history_users = array_map(function($values){
        		$array_values = (array)($values);
        		return $array_values;
        	}, $history_users->toArray());

        	$ticket_avialable_for = array_merge($ticket_avialable_for, $history_users);
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
        	return redirect('/')->with('error', 'You are not authorised to see this ticket!');
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

        $attachmentFile = DB::table('tickets_files')->where('ticket_id', '=', $id)->where('is_delete', 0)->get();

        $data = [
            'pageTitle'       => 'Details View Request ',
            'catList'         => Category::orderBy('id', 'DESC')->pluck('name', 'id'),
            'userList'        => User::pluck('users.name', 'users.id'),
            'ctrlName'        => 'Archive',
            'mthdName'        => 'ArchiveReport',
            'TicketInfo'      => $TicketInfo,
            'subcatList'      => $subcatList,
            'PreviousUrl'     => $PreviousUrl,
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

        return view('archive.live.details', compact('data'));
    }

}
