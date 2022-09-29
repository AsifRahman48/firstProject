<?php
namespace App\Services\V2;

use App\Contracts\ISchedulerOnlyDbBackupService;
use App\Repositories\V2\SchedulerFullBackupRepository;
use App\Repositories\V2\SchedulerOnlyDbBackupRepository;
use Illuminate\Http\Request;

class SchedulerOnlyDbBackupService implements ISchedulerOnlyDbBackupService
{
    protected $dbBackupRepository;

    public function __construct(SchedulerOnlyDbBackupRepository $dbBackupRepository)
    {
        $this->dbBackupRepository = $dbBackupRepository;
    }

    public function index()
    {
        return $this->dbBackupRepository->index();
    }

    public function store(Request $request)
    {
        return $this->dbBackupRepository->store($request);
    }

    public function backupsBetweenDays($oldDays, $nowDays)
    {
        return $this->dbBackupRepository->betweenDays($oldDays, $nowDays);
    }

    public function deleteBackupsBetweenDays($oldDays, $nowDays)
    {
        return $this->dbBackupRepository->deleteBetweenDays($oldDays, $nowDays);
    }
}
