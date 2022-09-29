<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateForeignKeyInTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign('tickets_cat_id_foreign');
            $table->dropForeign('tickets_sub_cat_id_foreign');
            // $table->dropForeign('tickets_initiator_id_foreign');
            // $table->dropForeign('tickets_recommender_id_foreign');

            $table->foreign('cat_id')->references('id')->on('categorys')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('sub_cat_id')->references('id')->on('sub_categorys')->onUpdate('cascade')->onDelete('restrict');
            // $table->foreign('initiator_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            // $table->foreign('recommender_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){}
}
