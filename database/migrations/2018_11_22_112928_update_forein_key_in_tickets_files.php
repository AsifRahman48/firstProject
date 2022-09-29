<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateForeinKeyInTicketsFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
 Schema::table('tickets_files', function (Blueprint $table) {
            // $table->dropForeign('tickets_cat_id_foreign');
            // $table->dropForeign('tickets_sub_cat_id_foreign');
            // $table->dropForeign('tickets_initiator_id_foreign');
            // $table->dropForeign('tickets_recommender_id_foreign');
 // $table->dropForeign('tickets_files_action_to_foreign');
 //            $table->foreign('ticket_id')->references('id')->on('tickets')->onUpdate('cascade')->onDelete('cascade');
        });


      
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
