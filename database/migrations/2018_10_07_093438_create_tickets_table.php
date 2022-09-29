<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tReference_no')->nullable();
            $table->unsignedInteger('cat_id');
            $table->unsignedInteger('sub_cat_id');
            $table->unsignedInteger('initiator_id');
            // $table->unsignedInteger('recommender_id');
            $table->enum('tStatus', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10])->comment('Status, 1:Draft by Initiator, 2:Submit by Initiator, 3:Draft by Approver, 4:Approved, 5:Rejected, 6:Request for Info, 7:Forward, 8:Forward(After Reject), 9:Forward(After Approved), 10:Disable');
            $table->string('tSubject');
            $table->text('tDescription')->nullable();
            $table->unsignedInteger('now_ticket_at')->nullable();
            $table->string('tFile')->nullable();
            $table->timestamps();
            $table->foreign('cat_id')->references('id')->on('categorys')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('sub_cat_id')->references('id')->on('sub_categorys')->onUpdate('cascade')->onDelete('cascade');
            // $table->foreign('initiator_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('is_delete')->default('0');
            // $table->foreign('recommender_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
