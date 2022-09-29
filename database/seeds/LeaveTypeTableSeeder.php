<?php

use Illuminate\Database\Seeder;

class LeaveTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('leave_types')->delete();

        \DB::table('leave_types')->insert(array (
            0 =>
                array (
                    'id' => 1,
                    'name' => 'Sick Leave',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            1 =>
                array (
                    'id' => 2,
                    'name' => 'Casual Leave',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),

            2 =>
                array (
                    'id' => 3,
                    'name' => 'Annual Leave',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            3 =>
                array (
                    'id' => 4,
                    'name' => 'Maternity Leave',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            4 =>
                array (
                    'id' => 5,
                    'name' => 'Tour Leave',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),

            5 =>
                array (
                    'id' => 6,
                    'name' => 'Training Leave',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            6 =>
                array (
                    'id' => 7,
                    'name' => 'Others',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
        ));
    }
}
