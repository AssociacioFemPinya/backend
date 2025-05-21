<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoardPositionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('board_position', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('board_id')->unsigned();
            $table->integer('event_id')->unsigned();
            $table->integer('casteller_id')->unsigned();
            $table->integer('board_event_id')->unsigned();
            $table->integer('colla_id')->unsigned();
            $table->integer('row_id')->unsigned();
            $table->enum('base', ['PINYA', 'FOLRE', 'MANILLES', 'PUNTALS'])->default('PINYA');
            $table->timestamps();

            $table->unique(['base', 'row_id', 'colla_id', 'board_event_id'], 'repeat_row');
            $table->unique(['casteller_id', 'board_event_id'], 'repeat_casteller');

            $table->foreign('board_id')
                ->references('id_board')->on('boards')
                ->onDelete('cascade');

            $table->foreign('event_id')
                ->references('id_event')->on('events')
                ->onDelete('cascade');

            $table->foreign('casteller_id')
                ->references('id_casteller')->on('castellers')
                ->onDelete('cascade');

            $table->foreign('colla_id')
                ->references('id_colla')->on('colles')
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
        Schema::dropIfExists('board_position');
    }
}
