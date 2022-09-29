<?php

use App\Models\V2\Setting;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Db backup scheduler default time to backup db by cron.
        Setting::updateOrCreate(
            [
                'key' => 'scheduler_only_db_backup',
            ],[
            'key' => 'scheduler_only_db_backup',
            'value' => json_encode(['time' => '07.00', 'is_disable' => 0, 'is_delete' => 0, 'delete_after_days' => 15]),
        ]);
        Setting::updateOrCreate(
            [
                'key' => 'scheduler_full_backup',
            ],[
            'key' => 'scheduler_full_backup',
            'value' => json_encode(['time' => '07.00', 'is_disable' => 0, 'is_delete' => 0, 'delete_after_days' => 15]),
        ]);

        Setting::updateOrCreate(
            [
                'key' => 'scheduler_ldap',
            ],[
            'key' => 'scheduler_ldap',
            'value' => json_encode(['time' => '07.00', 'is_disable' => 0]),
        ]);
    }
}
