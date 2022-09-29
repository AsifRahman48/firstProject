<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalColumnInTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'company_id')) {
                $table->integer('company_id')->after('tReference_no');
            }

            if (!Schema::hasColumn('tickets', 'step')) {
                $table->integer('step')->default(0)->after('now_ticket_at');
            }

            if (!Schema::hasColumn('tickets', 'thistory')) {
                $table->longText('thistory')->nullable()->after('step');
            }

            if (!Schema::hasColumn('tickets', 'priority')) {
                $table->integer('priority')->default(1)->after('thistory');
            }

            if (Schema::hasColumn('tickets', 'tFile')) {
                $table->dropColumn('tFile');
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
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'company_id')) {
                $table->dropColumn('company_id');
            }

            if (Schema::hasColumn('tickets', 'step')) {
                $table->dropColumn('step');
            }

            if (Schema::hasColumn('tickets', 'thistory')) {
                $table->dropColumn('thistory');
            }

            if (Schema::hasColumn('tickets', 'priority')) {
                $table->dropColumn('priority');
            }

            if (!Schema::hasColumn('tickets', 'tFile')) {
                $table->string('tFile');
            }
        });
    }
}
