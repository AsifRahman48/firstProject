<?php
namespace App\Contracts;

interface ISchedulerOnlyDbBackupService extends StorableInterface, IndexableInterface
{
    public function backupsBetweenDays($oldDays, $nowDays);

    public function deleteBackupsBetweenDays($oldDays, $nowDays);
}
