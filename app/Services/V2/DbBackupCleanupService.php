<?php
namespace App\Services\V2;

use App\Contracts\ISchedulerOnlyDbBackupService;
use Carbon\Carbon;
use Illuminate\Contracts\Config\Repository;
use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\BackupDestination\BackupCollection;
use Spatie\Backup\Tasks\Cleanup\CleanupStrategy;

class DbBackupCleanupService extends CleanupStrategy
{
    protected $dbBackupService;
    protected $nowDays;
    protected $oldDays;

    public function __construct(Repository $config, ISchedulerOnlyDbBackupService $dbBackupService)
    {
        parent::__construct($config);
        $this->dbBackupService = $dbBackupService;
    }

    public function deleteOldBackups(BackupCollection $backups)
    {
        $dbInfos = $this->getBackupInfoFromDb();
        $deleteBackups = $backups->map(function (Backup $backup) use($dbInfos) {
            $match = $dbInfos->where('path', $backup->path())->first();
            return $match ? $backup : null;
        });

        $deleteBackups->each(function ($item) {
            if (!is_null($item))
                $item->delete();
        });

        $this->deleteBackupsDbInfo();
    }

    public function setDays($days)
    {
        $this->nowDays = Carbon::now();
        $this->oldDays = Carbon::now()->subDays($days);
    }

    private function getBackupInfoFromDb()
    {
        return $this->dbBackupService->backupsBetweenDays($this->oldDays, $this->nowDays);
    }

    private function deleteBackupsDbInfo()
    {
        return $this->dbBackupService->deleteBackupsBetweenDays($this->oldDays, $this->nowDays);
    }
}
