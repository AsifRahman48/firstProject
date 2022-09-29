<?php

use Illuminate\Database\Seeder;

class ServerDnsNamesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('server_dns_names')->delete();

        \DB::table('server_dns_names')->insert(array (
            0 =>
                array (
                    'id' => 1,
                    'dns_name' => 'ams.psgbd.com',
                    'ip_address' => '103.17.180.148',
                    'status' => null,
                    'created_at' => '2021-04-01 17:19:44',
                    'updated_at' => '2021-04-01 17:19:44',
                ),
        ));
    }
}
