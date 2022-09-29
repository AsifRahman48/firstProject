<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(UserTableSeeder::class);
//        $this->call(SettingsTableSeeder::class);
//        $this->call(ServerDnsNamesTableSeeder::class);
//        $this->call(LeaveTypeTableSeeder::class);
        $this->call(CompanyName::class);

    }
}
