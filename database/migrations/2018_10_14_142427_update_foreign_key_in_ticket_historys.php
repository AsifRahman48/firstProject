<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateForeignKeyInTicketHistorys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_historys', function (Blueprint $table) {
            // $table->dropForeign('ticket_historys_ticket_id_foreign');
            // $table->dropForeign('ticket_historys_request_from_foreign');
            // $table->dropForeign('ticket_historys_action_to_foreign');

            // $table->foreign('ticket_id')->references('id')->on('tickets')->onUpdate('cascade')->onDelete('restrict');
            // $table->foreign('request_from')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            // $table->foreign('action_to')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){}
}
