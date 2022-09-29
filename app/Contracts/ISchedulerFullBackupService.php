<?php
namespace App\Contracts;

interface ISchedulerFullBackupService extends StorableInterface, IndexableInterface
{
    public function backupsBetweenDays($oldDays, $nowDays);

    public function deleteBackupsBetweenDays($oldDays, $nowDays);
}
