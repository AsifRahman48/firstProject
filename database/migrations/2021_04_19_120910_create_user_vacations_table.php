<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserVacationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_vacations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('leave_type_id');
            $table->unsignedInteger('user_id');
            $table->dateTime('from_date');
            $table->dateTime('to_date');
            $table->unsignedInteger('forward_user_id');
            $table->string('reason')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_vacations');
    }
}
