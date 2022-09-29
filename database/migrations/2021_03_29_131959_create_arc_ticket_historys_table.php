<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArcTicketHistorysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('arc_ticket_historys')) {
            Schema::create('arc_ticket_historys', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('ticket_id')->unsigned();
                $table->enum('tStatus', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11])->comment('Status, 1:Draft by Initiator, 2:Submit by Initiator, 3:Draft by Approver, 4:Approved, 5:Rejected, 6:Request for Info, 7:Forward, 8:Forward(After Reject), 9:Forward(After Approved), 10:Disable');
                $table->integer('action_to')->unsigned();
                $table->longText('tDescription')->nullable();
                $table->integer('created_by');
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
        Schema::dropIfExists('arc_ticket_historys');
    }
}
