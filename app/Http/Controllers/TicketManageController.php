<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Helper;
use App\Ticket;
use App\TicketEditHistory;
use App\Traits\AuditLogTrait;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class TicketManageController extends Controller
{
    use AuditLogTrait;

    public function index(Request $request)
    {
        if ($request->searchValue) {
            $tickets = Ticket::where('tReference_no', 'like', "%$request->searchValue%")->orWhere('id', $request->searchValue)->paginate(100);
        } else {
            $tickets = Ticket::where('tStatus', '!=', 1)->where('tStatus', '!=', 4)->paginate(100);
        }

        $data = [
            'pageTitle' => 'Tickets',
            'ctrlName' => 'ticketManage',
            'tickets' => $tickets
        ];
        $statusList = Helper::getStatusList();

        return view('manage_ticket.list', compact('data', 'statusList'));
    }

    public function ticketView($id)
    {
        $currentUrl = URL::current();
        if (Str::contains($currentUrl, 'manage_ticket_edit')) {
            $page = "edit";
        } else {
            $page = "show";
        }

        $ticketInfo = Ticket::where('id', '=', $id)->where('tStatus', '!=', 1)->first();

        # User type 1 - Admin
        if (Auth::user()->user_type != 1){
            return redirect('/')->with('error', 'You are not authorised to see this ticket!');
        }

        $ticketData = $this->getTicketRelatedData($id);

        $data = [
            'pageTitle' => 'Details View Request',
            'userList'  => User::pluck('users.name', 'users.id'),
            'ctrlName'  => 'ticketManage',
            'ticketInfo'=> $ticketInfo,
            'approverList'=> $ticketData['approverList'],
            'recommenderList'=> $ticketData['recommenderList'],
            'attachmentFile'=> $ticketData['attachmentFile'],
            'previousComment'=> $ticketData['previousComment'],
            'page' => $page
        ];

        return view('manage_ticket.edit', compact('data'));
    }

    public function getTicketRelatedData($id)
    {
        $recommenderList = User::select('users.name','users.id')
            ->join('ticket_approve as TA','TA.user_id','=','users.id')
            ->where('TA.user_type', '=',1)
            ->where('TA.ticket_id', '=',$id)
            ->get();

        $approverList = User::select('users.name','users.id')
            ->join('ticket_approve as TA','TA.user_id','=','users.id')
            ->where('TA.user_type', '=',2)
            ->where('TA.ticket_id', '=',$id)
            ->get();

        $previousComment = DB::table('ticket_historys as TH')
            ->join('users as UI','TH.action_to','=','UI.id')
            ->select('TH.*','UI.name as User_name')
            ->where('TH.ticket_id', '=',$id)
            ->orderBy('TH.id', 'asc')
            ->get();

        $attachmentFile = DB::table('tickets_files')->where('ticket_id', '=',$id)->where('is_delete',0)->get();

        return [
            'recommenderList' => $recommenderList,
            'approverList' => $approverList,
            'previousComment' => $previousComment,
            'attachmentFile' => $attachmentFile
        ];
    }

    public function updateViewStatus(Request $request, $id)
    {
        $ticket = Ticket::findorfail($id);
        $ticket->update(['is_viewed' => $request->is_viewed]);

        $this->logStore('updated','ticket',"$ticket->tSubject( $ticket->tReference_no ) ticket updated.",'manage tickets');

        return redirect()->back()->with('success', 'View Status Updated Successfully');
    }

    public function ticketUpdate(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
           'description' => 'required'
        ]);

        DB::transaction(function () use ($request, $id)
        {
            $ticket = Ticket::where('id', $id)->first();

            $filesInfo = [];
            if ($request->hasfile('tFile')) {
                $files = DB::table('tickets_files')->where('ticket_id', $id)->get();
                if(!empty($files)) {
                    foreach ($files as $file) {
                        $filesInfo[] = ['file_name' => $file->file_name, 'folder' => $file->folder];
                    }
                }

                $this->uploadFile($request, $id);
            }

            $data = [
                'ticket_id' => $id,
                'edited_by' =>  Auth::user()->id,
                'description' => $ticket->tDescription,
                'files' => !empty($files) ? json_encode($filesInfo) : null
            ];

            $ticket->update(['tDescription' => $request->description]);

            TicketEditHistory::create($data);

            $this->logStore('updated','ticket',"$ticket->tSubject( $ticket->tReference_no ) ticket updated.",'manage tickets');
        });


        return redirect()->route('get_manage_tickets')->with('success', 'Ticket Updated Successfully');
    }

    public function ticketEditView($id)
    {
        $ticketInfo = TicketEditHistory::where('id', $id)->first();

        $data = [
            'pageTitle' => 'Edit History View',
            'ticketInfo' => $ticketInfo
        ];

        return view('manage_ticket.view', compact('data'));
    }

    private function uploadFile($request, $ticketid)
    {
        $fileData = array();

        /* TODO:
            1. Take multiple file from user
            2. File delete functionality
        */
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
}
