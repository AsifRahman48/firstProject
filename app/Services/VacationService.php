<?php


namespace App\Services;

use App\User;
use Exception;
use App\Ticket;
use Carbon\Carbon;
use App\UserVacation;
use App\TicketHistory;
use App\TicketApprove as Approve;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;

class VacationService
{
    const TYPE_FORWARDED = 3;

    # History log user status
    const STATUS_INITIATED = 0;
    const STATUS_PENDING = 1;
    const STATUS_FORWARD = 3;

    private $ticketHistoryService;

    public function __construct(TicketHistoryService $ticketHistoryService)
    {
        $this->ticketHistoryService = $ticketHistoryService;
    }

    public function checkUserHasSetVacationOrNot($userId): bool
    {
        $recentVacation = UserVacation::where('user_id', $userId)->where('status', 'submitted')->orderBy('id', 'desc')->first();

        if (!empty($recentVacation)) {
            return $recentVacation->to_date > Carbon::now()->format('Y-m-d');
        } else {
            return false;
        }
    }

    public function forwardTicket($inputData)
    {
        $vacation = UserVacation::where('user_id', $inputData['now_ticket_at'])->where('status', 'submitted')->latest()->first();
        $inputData['forwardUser'] = $vacation->forward_user_id;
        $inputData['leave_type'] = $vacation->leaveType->name;
        $tStatus = $inputData['tStatus'] == 6 ? 6 : 7;

        # check cycle is done
        $checkCycleDone = Approve::where('ticket_id', $inputData['id'])->where('action', 0)->first();
        if (empty($checkCycleDone) && ($inputData['initiator_id'] == $inputData['now_ticket_at'])) {
            $redirect_msg = 'Successfully Updated';
            return redirect('request/inbox')->with('status', $redirect_msg);
        }

        # Check empty forwarded user
        if (empty($inputData['forwardUser'])) {
            return redirect('/request/inbox')->with('error', 'User is on vacation & not have any forward user');
        }

        # Check that user should not forward ticket to Initiator
        $initiator_forwarded = Ticket::where('id', '=', $inputData['id'])->where('initiator_id', '=', $inputData['forwardUser'])->first();

        # check initiator and forwared user is the same
        if (!empty($initiator_forwarded)) {
            return redirect('/request/inbox')->with('error', 'You can not forward ticket to Initiatior.');
        }

        Approve::where('ticket_id', '=', $inputData['id'])->where('user_id', '=', $inputData['now_ticket_at'])->update(['action' => 1]);
                   
        $update_nextUser = Ticket::find($inputData['id']);
        $update_nextUser->now_ticket_at = $inputData['forwardUser'];
        $update_nextUser->tStatus = $tStatus;
        $update_nextUser->save();

        $this->updateTicketHistory($inputData, $tStatus);

        $this->mailToNextUser($inputData, $update_nextUser);

        $this->updateHistoryJsonOfTicketTable($inputData);

        $ticketData = Ticket::find($inputData['id']);
        if($this->checkUserHasSetVacationOrNot($ticketData['now_ticket_at'])) {
            return $this->forwardTicket($ticketData);
        } else {
            return true;
        }
    }

    public function updateTicketHistory($inputData, $tStatus)
    {
        $update_historys = new TicketHistory();
        $update_historys->ticket_id = $inputData['id'];
        $update_historys->tDescription = null;
        $update_historys->tStatus = $tStatus;
        $update_historys->action_to = Auth::id();
        $update_historys->created_by = Auth::id();
        $update_historys->save();
    }

    public function mailToNextUser($inputData, $update_nextUser)
    {
        $userInfoMail = User::find($inputData['forwardUser']);
        if (!empty($userInfoMail->email)) {
            $id = $inputData['id'];
            $url = url('request/details/') . '/' . $id;
            $maildata = ['URL' => $url, 'name' => $userInfoMail->name, 'tReference_no' => $update_nextUser->tReference_no, 'onlySubject' => $update_nextUser->tSubject];

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
                # continue
            }
        }
    }

    public function updateHistoryJsonOfTicketTable($inputData)
    {
        $updateTicketLog = Ticket::find($inputData['id']);
        $log = json_decode($updateTicketLog->thistory, true);
        $logInfo = array();
        foreach ($log as $key => $HistoryInfo) {

            # For existing ticket
            # Get user type id
            if (isset($HistoryInfo['user_type']) && !empty($HistoryInfo['user_type'])) {
                $existing_user_type_id = $this->ticketHistoryService->UserTypeIdHistoryLog($HistoryInfo['user_type']);
            } else {
                $existing_user_type_id = 101;
            }
            # Get user status id
            if (isset($HistoryInfo['user_status']) && !empty($HistoryInfo['user_status'])) {
                $existing_status_type_id = $this->ticketHistoryService->UserStatusIdHistoryLog($HistoryInfo['user_status']);
            } else {
                $existing_status_type_id = 102;
            }

            if ($HistoryInfo['user_id'] == $inputData['now_ticket_at'] && $HistoryInfo['user_action'] == 0) {

                $logInfo[] = [
                    'user_id' => $HistoryInfo['user_id'],
                    'user_name' => $HistoryInfo['user_name'],
                    'user_type' => $HistoryInfo['user_type'],
                    'user_type_id' => !empty($HistoryInfo['user_type_id']) ? $HistoryInfo['user_type_id'] : $existing_user_type_id,
                    'user_status' => 'Forward (' . $inputData['leave_type'] . ')',
                    'user_status_id' => self::STATUS_FORWARD,
                    'user_action' => 1,
                    'date' => date('d-m-Y H:i:s')
                ];

                $userInfo = User::where('id', $inputData['forwardUser'])->first();

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

        $updateTicketLog->thistory = json_encode($logInfo);

        return $updateTicketLog->save();
    }
}
