<?php
namespace App\Services\V2;

use App\Contracts\ISchedulerFullBackupService;
use App\Repositories\V2\SchedulerFullBackupRepository;
use Illuminate\Http\Request;

class SchedulerFullBackupService implements ISchedulerFullBackupService
{
    protected $fullBackupRepository;

    public function __construct(SchedulerFullBackupRepository $fullBackupRepository)
    {
        $this->fullBackupRepository = $fullBackupRepository;
    }

    public function index()
    {
        return $this->fullBackupRepository->index();
    }

    public function store(Request $request)
    {
        return $this->fullBackupRepository->store($request);
    }

    public function backupsBetweenDays($oldDays, $nowDays)
    {
        return $this->fullBackupRepository->betweenDays($oldDays, $nowDays);
    }

    public function deleteBackupsBetweenDays($oldDays, $nowDays)
    {
        return $this->fullBackupRepository->deleteBetweenDays($oldDays, $nowDays);
    }
}
