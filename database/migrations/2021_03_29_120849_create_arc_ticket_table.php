<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArcTicketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('arc_ticket')) {
            Schema::create('arc_ticket', function (Blueprint $table) {
                $table->increments('id');
                $table->string('tReference_no')->nullable();
                $table->integer('company_id');
                $table->integer('cat_id')->unsigned();
                $table->integer('sub_cat_id')->unsigned();
                $table->integer('initiator_id')->unsigned();
                $table->enum('tStatus', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11])->comment('Status, 1:Draft by Initiator, 2:Submit by Initiator, 3:Draft by Approver, 4:Approved, 5:Rejected, 6:Request for Info, 7:Forward, 8:Forward(After Reject), 9:Forward(After Approved), 10:Disable');
                $table->longText('tSubject');
                $table->longText('tDescription')->nullable();
                $table->integer('now_ticket_at')->nullable();
                $table->integer('step')->default(0);
                $table->longText('thistory')->nullable();
                $table->integer('priority');
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
        Schema::dropIfExists('arc_ticket');
    }
}
