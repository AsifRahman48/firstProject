<?php

namespace App\Http\Controllers\V2;

use App\Contracts\ISchedulerFullBackupService;
use App\Contracts\ISchedulerOnlyDbBackupService;
use App\Traits\AuditLogTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SchedulerBackupController extends Controller
{
    use AuditLogTrait;

    protected $fullBackupService;
    protected $dbBackupService;

    public function __construct(ISchedulerFullBackupService $fullBackupService, ISchedulerOnlyDbBackupService $dbBackupService)
    {
        $this->fullBackupService = $fullBackupService;
        $this->dbBackupService = $dbBackupService;
    }

    public function fullBackup()
    {
        $data = [
            'pageTitle' => 'Full Backups',
            'backups' => $this->fullBackupService->index(),
            'scheduler' => json_decode(setting('scheduler_full_backup')),
        ];
        return view('v2.backup.full-backup', compact('data'));
    }

    public function storeFullBackupSetting(Request $request)
    {
        $inputs = $request->only(['time', 'is_disable', 'is_delete', 'delete_after_days']);

        $setting = storeSetting('scheduler_full_backup', json_encode($inputs));

        $this->logStore('updated','auto full backup',"Auto full backup setting updated.",'auto full backup');

        return response()->json([
            'data' => $setting,
        ], 200);
    }

    public function dbBackup()
    {
        $data = [
            'pageTitle' => 'DB Backups',
            'backups' => $this->dbBackupService->index(),
            'scheduler' => json_decode(setting('scheduler_only_db_backup')),
        ];
        return view('v2.backup.db-backup', compact('data'));
    }

    public function storeDbBackupSetting(Request $request)
    {
        $inputs = $request->only(['time', 'is_disable', 'is_delete', 'delete_after_days']);

        $setting = storeSetting('scheduler_only_db_backup', json_encode($inputs));

        $this->logStore('updated','auto db backup',"Auto db backup setting updated.",'auto db backup');

        return response()->json([
            'data' => $setting,
        ], 200);
    }
}
