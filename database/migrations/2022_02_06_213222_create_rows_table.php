<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rows', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('colla_id')->unsigned();
            $table->integer('board_id')->unsigned();
            $table->integer('div_id')->unsigned();
            $table->string('row');
            $table->integer('cord')->unsigned()->default(0);
            $table->enum('side', ['RIGHT', 'LEFT', '']);
            $table->string('position');
            $table->enum('base', ['PINYA', 'FOLRE', 'MANILLES', 'PUNTALS']);
            $table->timestamps();

            $table->foreign('colla_id')
                ->references('id_colla')->on('colles')
                ->onDelete('cascade');

            $table->foreign('board_id')
                ->references('id_board')->on('boards')
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
        Schema::dropIfExists('rows');
    }
}
