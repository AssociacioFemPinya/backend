<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRondesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rondes', function (Blueprint $table) {
            $table->increments('id_ronda')->unsigned();
            $table->integer('event_id')->unsigned()->index();
            $table->integer('board_event_id')->unsigned();
            $table->tinyInteger('ronda')->unsigned();
            $table->timestamps();

            $table->unique(['ronda', 'board_event_id'], 'ronda_board_event_unique');

            $table->foreign('event_id')
                ->references('id_event')->on('events')
                ->onDelete('cascade');

            $table->foreign('board_event_id')
                ->references('id')->on('board_event')
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
        Schema::dropIfExists('rondes');
    }
}
