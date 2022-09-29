<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActionColumnInTicketApproveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_approve', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_approve', 'action')) {
                $table->integer('action')->default(0)->after('user_type');
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
        Schema::table('ticket_approve', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_approve', 'action')) {
                $table->dropColumn('action');
            }
        });
    }
}
