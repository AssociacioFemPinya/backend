<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->increments('id_attendance');
            $table->integer('id_attendance_external')->unsigned()->nullable();
            $table->integer('event_id')->unsigned();
            $table->integer('casteller_id')->unsigned();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('status_verified')->nullable();
            $table->integer('companions')->unsigned()->nullable();
            $table->tinyInteger('source')->nullable();
            $table->json('options')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->foreign('event_id')
                ->references('id_event')->on('events')
                ->onDelete('cascade');

            $table->foreign('casteller_id')
                ->references('id_casteller')->on('castellers')
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
        Schema::dropIfExists('attendance');
    }
}
