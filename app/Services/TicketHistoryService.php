<?php


namespace App\Services;


class TicketHistoryService
{
    # History log user type
    const TYPE_INITIATOR = 0;
    const TYPE_RECOMMENDER = 1;
    const TYPE_APPROVER = 2;

    # History log user status
    const STATUS_INITIATED = 0;
    const STATUS_PENDING = 1;
    const STATUS_APPROVED = 2;

    public function UserTypeIdHistoryLog($history_user_type): int
    {

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

    public function UserStatusIdHistoryLog($history_user_status): int
    {

        if ($history_user_status == 'Initiated' ) {
            $existing_user_status_id = self::STATUS_INITIATED;
        } 
        elseif( $history_user_status == 'Pending' ) {
            $existing_user_status_id = self::STATUS_PENDING;
        }
        elseif ($history_user_status == 'Approved') {
            $existing_user_status_id = self::STATUS_APPROVED;
        }
        else {
            $existing_user_status_id = 102;
        }

        return $existing_user_status_id;
    }
}
