<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketHistorysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_historys', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ticket_id');
            $table->enum('tStatus', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10])->comment('Status, 1:Draft by Initiator, 2:Submit by Initiator, 3:Draft by Approver, 4:Approved, 5:Rejected, 6:Request for Info, 7:Forward, 8:Forward(After Reject), 9:Forward(After Approved), 10:Disable');
            // $table->unsignedInteger('request_from');
            $table->unsignedInteger('action_to');
            $table->text('tDescription')->nullable();
            $table->timestamps();
            // $table->foreign('ticket_id')->references('id')->on('tickets')->onUpdate('cascade')->onDelete('cascade');
            // $table->foreign('action_to')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            // $table->foreign('request_to')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_historys');
    }
}
