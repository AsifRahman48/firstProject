<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyNameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('company_name')) {
            Schema::create('company_name', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('short_name');
                $table->longText('logo')->nullable();
                $table->date('active_date')->nullable();
                $table->date('deactive_date')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_name');
    }
}
