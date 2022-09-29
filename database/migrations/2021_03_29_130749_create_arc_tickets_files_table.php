<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArcTicketsFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('arc_tickets_files')) {
            Schema::create('arc_tickets_files', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('ticket_id')->unsigned();
                $table->text('file_name');
                $table->text('text');
                $table->string('folder')->nullable();
                $table->timestamps();
                $table->integer('is_delete')->default(0);
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
        Schema::dropIfExists('arc_tickets_files');
    }
}
