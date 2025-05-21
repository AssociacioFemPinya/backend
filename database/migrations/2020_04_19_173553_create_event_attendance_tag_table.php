<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventAttendanceTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_attendance_tag', function (Blueprint $table) {
            $table->integer('event_id')->unsigned();
            $table->integer('tag_id')->unsigned();

            $table->foreign('event_id')
                ->references('id_event')->on('events')
                ->onDelete('cascade');

            $table->foreign('tag_id')
                ->references('id_tag')->on('tags')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_attendance_tag');
    }
}
