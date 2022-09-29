<?php

namespace App\Observers;

class AuditLog
{
    public function created(\Spatie\Activitylog\Models\Activity $log)
    {
        $ip = trim(shell_exec("dig +short myip.opendns.com @resolver1.opendns.com")) ?? request()->ip();

        $log->update([
            'ip' => $ip
        ]);
    }
}
