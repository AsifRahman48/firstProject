<?php

namespace App\Console\Commands;

use App\Contracts\ISchedulerFullBackupService;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class SchedulerFullBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scheduler:fullbackup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the full backup by running this command in scheduler.';
    private $backupService;

    /**
     * Create a new command instance.
     *
     * @param ISchedulerFullBackupService $backupService
     */
    public function __construct(ISchedulerFullBackupService $backupService)
    {
        parent::__construct();
        $this->backupService = $backupService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->runBackupCommand();
        $newFile = $this->getLastStorageFile();
        $fileName = $this->getFileName($newFile);
        $fileSize = $this->getFileSize($newFile);
        $this->backupService->store(new Request([
            'name' => $fileName,
            'size' => $fileSize,
            'path' => $newFile
        ]));
    }

    private function runBackupCommand()
    {
        Artisan::call("backup:run",
            [
                '--disable-notifications' => true,
                '--only-to-disk' => 'schedulerfull',
            ]);
    }

    private function getLastStorageFile()
    {
        $files = Storage::disk('schedulerfull')->allFiles(config('custom.settings.backup'));
        rsort($files);
        return $files[0];
    }

    private function getFileName($newFile)
    {
        $array = explode('/', $newFile);
        return $array[1];
    }

    private function getFileSize($newFile)
    {
        $size = Storage::disk('schedulerfull')->size($newFile);
        $base = log($size) / log(1024);
        $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');
        return round(pow(1024, $base - floor($base)), 2) .$suffixes[floor($base)];
    }
}
