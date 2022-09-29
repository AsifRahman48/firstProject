<?php
namespace App\Repositories\V2;

use App\Contracts\IndexableInterface;
use App\Contracts\StorableInterface;
use App\Models\V2\SchedulerFullBackup;
use Illuminate\Http\Request;

class SchedulerFullBackupRepository implements StorableInterface, IndexableInterface
{
    protected $fullBackup;

    public function __construct(SchedulerFullBackup $fullBackup)
    {
        $this->fullBackup = $fullBackup;
    }

    public function index()
    {
        return $this->fullBackup->paginate(15);
    }

    public function store(Request $request)
    {
        $inputs = $request->only($this->fullBackup->fillable);
        return $this->fullBackup->create($inputs);
    }

    public function betweenDays($oldDays, $nowDays)
    {
        return $this->fullBackup->whereBetween('created_at', [$oldDays, $nowDays])->get();
    }

    public function deleteBetweenDays($oldDays, $nowDays)
    {
        return $this->fullBackup->whereBetween('created_at', [$oldDays, $nowDays])->delete();
    }
}
