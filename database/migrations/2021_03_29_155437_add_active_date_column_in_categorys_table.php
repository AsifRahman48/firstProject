<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActiveDateColumnInCategorysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categorys', function (Blueprint $table) {
            if (!Schema::hasColumn('categorys', 'active_date')) {
                $table->date('active_date')->nullable()->after('name');
            }

            if (!Schema::hasColumn('categorys', 'deactive_date')) {
                $table->date('deactive_date')->nullable()->after('active_date');
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
        Schema::table('categorys', function (Blueprint $table) {
            if (Schema::hasColumn('categorys', 'active_date')) {
                $table->dropColumn('active_date');
            }

            if (Schema::hasColumn('categorys', 'deactive_date')) {
                $table->dropColumn('deactive_date');
            }
        });
    }
}
