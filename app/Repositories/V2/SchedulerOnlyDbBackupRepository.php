<?php
namespace App\Repositories\V2;

use App\Contracts\IndexableInterface;
use App\Contracts\StorableInterface;
use App\Models\V2\SchedulerOnlyDbBackup;
use Illuminate\Http\Request;

class SchedulerOnlyDbBackupRepository implements StorableInterface, IndexableInterface
{
    protected $dbBackup;

    public function __construct(SchedulerOnlyDbBackup $dbBackup)
    {
        $this->dbBackup = $dbBackup;
    }

    public function index()
    {
        return $this->dbBackup->paginate(15);
    }

    public function store(Request $request)
    {
        $inputs = $request->only($this->dbBackup->fillable);
        return $this->dbBackup->create($inputs);
    }

    public function betweenDays($oldDays, $nowDays)
    {
        return $this->dbBackup->whereBetween('created_at', [$oldDays, $nowDays])->get();
    }

    public function deleteBetweenDays($oldDays, $nowDays)
    {
        return $this->dbBackup->whereBetween('created_at', [$oldDays, $nowDays])->delete();
    }
}
