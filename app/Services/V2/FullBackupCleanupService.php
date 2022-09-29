<?php
namespace App\Services\V2;

use App\Contracts\ISchedulerFullBackupService;
use App\Contracts\ISchedulerOnlyDbBackupService;
use Carbon\Carbon;
use Illuminate\Contracts\Config\Repository;
use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\BackupDestination\BackupCollection;
use Spatie\Backup\Tasks\Cleanup\CleanupStrategy;

class FullBackupCleanupService extends CleanupStrategy
{
    protected $nowDays;
    protected $oldDays;
    protected $fullBackupService;

    public function __construct(Repository $config, ISchedulerFullBackupService $fullBackupService)
    {
        parent::__construct($config);
        $this->fullBackupService = $fullBackupService;
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
        return $this->fullBackupService->backupsBetweenDays($this->oldDays, $this->nowDays);
    }

    private function deleteBackupsDbInfo()
    {
        return $this->fullBackupService->deleteBackupsBetweenDays($this->oldDays, $this->nowDays);
    }
}
