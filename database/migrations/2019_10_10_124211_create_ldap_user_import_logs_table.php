<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLdapUserImportLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('ldap_user_import_logs')) {
            Schema::create('ldap_user_import_logs', function (Blueprint $table) {
                $table->increments('id');
                $table->string('imported_by');
                $table->date('date');
                $table->integer('inserted_users');
                $table->integer('updated_users');
                $table->enum('type', ['manual', 'auto']);
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
        Schema::dropIfExists('ldap_user_import_logs');
    }
}
