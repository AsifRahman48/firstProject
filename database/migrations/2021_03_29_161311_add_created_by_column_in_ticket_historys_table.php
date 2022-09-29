<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatedByColumnInTicketHistorysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_historys', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_historys', 'created_by')) {
                $table->integer('created_by')->after('tDescription');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_historys', function (Blueprint $table) {
            if (Schema::hasColumn('ticket_historys', 'created_by')) {
                $table->dropColumn('created_by');
            }
        });
    }
}
