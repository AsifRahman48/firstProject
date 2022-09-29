<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalColumnInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'user_name')) {
                $table->string('user_name')->after('name')->nullable();
            }

            if (!Schema::hasColumn('users', 'title')) {
                $table->string('title')->after('user_type')->nullable();
            }

            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->after('title')->nullable();
            }

            if (!Schema::hasColumn('users', 'telephonenumber')) {
                $table->string('telephonenumber')->after('department')->nullable();
            }

            if (!Schema::hasColumn('users', 'company_name')) {
                $table->string('company_name')->after('telephonenumber')->nullable();
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
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'user_name')) {
                $table->dropColumn('user_name');
            }

            if (Schema::hasColumn('users', 'title')) {
                $table->dropColumn('title');
            }

            if (Schema::hasColumn('users', 'department')) {
                $table->dropColumn('department');
            }

            if (Schema::hasColumn('users', 'telephonenumber')) {
                $table->dropColumn('telephonenumber');
            }

            if (Schema::hasColumn('users', 'company_name')) {
                $table->dropColumn('company_name');
            }
        });
    }
}
