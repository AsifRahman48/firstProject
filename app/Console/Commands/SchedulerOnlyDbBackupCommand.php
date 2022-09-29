<?php

namespace App\Console\Commands;

use App\Contracts\ISchedulerOnlyDbBackupService;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class SchedulerOnlyDbBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scheduler:onlydb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the db backup by running this command in scheduler.';
    private $dbBackupService;

    /**
     * Create a new command instance.
     * @param ISchedulerOnlyDbBackupService $dbBackupService
     */
    public function __construct(ISchedulerOnlyDbBackupService $dbBackupService)
    {
        parent::__construct();
        $this->dbBackupService = $dbBackupService;
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
        $this->dbBackupService->store(new Request([
            'name' => $fileName,
            'size' => $fileSize,
            'path' => $newFile
        ]));
    }

    private function runBackupCommand()
    {
        Artisan::call("backup:run",
            [
                '--only-db' => true,
                '--disable-notifications' => true,
                '--only-to-disk' => 'scheduleronlydb',
            ]);
    }

    private function getLastStorageFile()
    {
        $files = Storage::disk('scheduleronlydb')->allFiles(config('custom.settings.backup'));
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
        $size = Storage::disk('scheduleronlydb')->size($newFile);
        $base = log($size) / log(1024);
        $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');
        return round(pow(1024, $base - floor($base)), 2) .$suffixes[floor($base)];
    }
}
