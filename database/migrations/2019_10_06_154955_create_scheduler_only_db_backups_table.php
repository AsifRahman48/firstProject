<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulerOnlyDbBackupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('scheduler_only_db_backups')) {
            Schema::create('scheduler_only_db_backups', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('size');
                $table->string('path');
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
        Schema::dropIfExists('scheduler_only_db_backups');
    }
}
