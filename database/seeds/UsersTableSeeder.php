<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\User;
// use App\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users')->delete();
        // $role_admin       = Role::where('name', 'Admin')->first();
        // $role_recommender = Role::where('name', 'Recommender')->first();
        // $role_initiator   = Role::where('name', 'Initiator')->first();

        $admin = new User();
        $admin->name = 'Mr. Admin';
        $admin->email = 'admin@mail.com';
        $admin->user_name = 'admin';
        $admin->password = Hash::make('123456');
        $admin->user_type ='1';
        $admin->save();
        // $admin->roles()->attach($role_admin);

        $recommender = new User();
        $recommender->name = 'Mr. Recommender';
        $recommender->email = 'recommender@mail.com';
        $recommender->user_name = 'recommender';
        $recommender->password = Hash::make('123456');
        $recommender->user_type ='0';
        $recommender->save();
        // $recommender->roles()->attach($role_recommender);

        $initiator = new User();
        $initiator->name = 'Mr. Approver';
        $initiator->email = 'approver@mail.com';
        $initiator->user_name = 'approver';
        $initiator->password = Hash::make('123456');
        $initiator->user_type ='0';
        $initiator->save();
        // $initiator->roles()->attach($role_initiator);
    }
}
