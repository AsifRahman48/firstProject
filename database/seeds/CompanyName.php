<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use  Illuminate\Support\Facades\DB;
class CompanyName extends Seeder
{
    public function run()
    {
        $faker=Faker::create();
      for($i=0;$i<=10;$i++)
      {
          DB::table('company_name')->insert([
              'name'=>$faker->name,
              'short_name'=>'BS-23',
              'logo'=>'logo',

          ]);
      }
    }
}
