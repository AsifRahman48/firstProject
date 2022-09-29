<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuditLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ip')->nullable();
            $table->unsignedBigInteger('causer_id')->nullable();
            $table->string('activity_name')->nullable();
            $table->string('activity_type')->nullable();
            $table->string('menu_journey')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['activity_name', 'activity_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
}
