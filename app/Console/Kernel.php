<?php

namespace App\Console;

use App\Console\Commands\BackupCleanupCommand;
use App\Console\Commands\LdapUserImportCommand;
use App\Console\Commands\SchedulerFullBackupCommand;
use App\Console\Commands\SchedulerOnlyDbBackupCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Schema;
use Spatie\Backup\Commands\BackupCommand;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        SchedulerFullBackupCommand::class,
        SchedulerOnlyDbBackupCommand::class,
        LdapUserImportCommand::class,
        BackupCleanupCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $ldapImportSetting = Schema::hasTable('settings') ? setting('scheduler_ldap') : null;
        $fullBackupSetting = Schema::hasTable('settings') ? setting('scheduler_full_backup') : null;
        $dbBackupSetting = Schema::hasTable('settings') ? setting('scheduler_only_db_backup') : null;

        if ($ldapImportSetting) {
            $ldapSchedule = json_decode($ldapImportSetting);
            if ($ldapSchedule->is_disable == 0) {
                $schedule->command(LdapUserImportCommand::class)
                    ->dailyAt($ldapSchedule->time);
            }
        }

        if ($fullBackupSetting) {
            $fullBackup = json_decode($fullBackupSetting);
            if ($fullBackup->is_disable == 0) {
                $schedule->command(SchedulerFullBackupCommand::class)
                    ->dailyAt($fullBackup->time);
            }

            if ($fullBackup->is_delete == 1) {
                $schedule->command(BackupCleanupCommand::class, ['schedulerfull', $fullBackup->delete_after_days])->daily();
            }
        }

        if ($dbBackupSetting) {
            $dbBackup = json_decode($dbBackupSetting);
            if ($dbBackup->is_disable == 0) {
                $schedule->command(SchedulerOnlyDbBackupCommand::class)
                    ->dailyAt($dbBackup->time);
            }

            if ($dbBackup->is_delete == 1) {
                $schedule->command(BackupCleanupCommand::class, ['scheduleronlydb', $dbBackup->delete_after_days])->daily();
            }
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
