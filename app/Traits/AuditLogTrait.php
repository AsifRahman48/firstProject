<?php

namespace App\Traits;

use App\AuditLog;

trait AuditLogTrait
{
    public function logStore($activityType, $activityName, $description, $menu_journey)
    {
        AuditLog::create([
            'ip' => trim(shell_exec("dig +short myip.opendns.com @resolver1.opendns.com")) ?? request()->ip(),
            'causer_id' => auth()->id() ?? null,
            'activity_name' => $activityName,
            'activity_type' => $activityType,
            'menu_journey' => $menu_journey,
            'description' => $description,
        ]);
    }
}
