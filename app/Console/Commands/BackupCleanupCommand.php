<?php

namespace App\Console\Commands;

use App\Services\V2\DbBackupCleanupService;
use App\Services\V2\FullBackupCleanupService;
use Illuminate\Console\Command;
use Spatie\Backup\BackupDestination\BackupDestination;

class BackupCleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:cleanup {disk-name} {days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean old backup files.';
    /**
     * @var \Illuminate\Config\Repository
     */
    private $config;
    /**
     * @var DbBackupCleanupService
     */
    private $dbCleanupService;
    /**
     * @var FullBackupCleanupService
     */
    private $fullCleanupService;

    /**
     * Create a new command instance.
     *
     * @param DbBackupCleanupService $dbCleanupService
     * @param FullBackupCleanupService $fullCleanupService
     */
    public function __construct(DbBackupCleanupService $dbCleanupService, FullBackupCleanupService $fullCleanupService)
    {
        parent::__construct();
        $this->config = config('backup')['backup'];
        $this->dbCleanupService = $dbCleanupService;
        $this->fullCleanupService = $fullCleanupService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $diskName = $this->argument('disk-name');
        $days = $this->argument('days');
        $destination = $this->getDestination($diskName);

        if ($destination && $diskName == 'scheduleronlydb') {
            $this->dbCleanupService->setDays($days);
            $this->dbCleanupService->deleteOldBackups($destination->backups());
        }

        if ($destination && $diskName == 'schedulerfull') {
            $this->fullCleanupService->setDays($days);
            $this->fullCleanupService->deleteOldBackups($destination->backups());
        }
    }

    private function getDestination($diskName)
    {
        $isDisk = in_array($diskName, $this->config['destination']['disks']);

        if ($isDisk) {
            return BackupDestination::create($diskName, $this->config['name']);
        }
    }
}
