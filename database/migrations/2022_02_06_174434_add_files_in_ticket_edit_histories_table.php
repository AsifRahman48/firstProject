<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFilesInTicketEditHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_edit_histories', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_edit_histories', 'files')) {
                $table->text('files')->nullable();
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
        Schema::table('ticket_edit_histories', function (Blueprint $table) {
            if (Schema::hasColumn('ticket_edit_histories', 'files')) {
                $table->dropColumn('files');
            }
        });
    }
}
