<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFolderColumnInTicketsFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets_files', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets_files', 'folder')) {
                $table->string('folder')->nullable()->after('file_type');
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
        Schema::table('tickets_files', function (Blueprint $table) {
            if (Schema::hasColumn('tickets_files', 'folder')) {
                $table->dropColumn('folder');
            }
        });
    }
}
