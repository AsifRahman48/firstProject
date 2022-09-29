<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArcTicketApproveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('arc_ticket_approve')) {
            Schema::create('arc_ticket_approve', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('ticket_id')->unsigned();
                $table->integer('user_id')->unsigned();
                $table->integer('user_type')->comment('2 = approver, 1 = recommender');
                $table->integer('action')->default(0);
                $table->timestamps();
                $table->integer('is_delete')->default(0);
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
        Schema::dropIfExists('arc_ticket_approve');
    }
}
