<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boards', function (Blueprint $table) {
            $table->increments('id_board');
            $table->integer('colla_id')->unsigned();
            $table->string('name', 100);
            $table->enum('type', ['PINYA','FOLRE','MANILLES','PUNTALS']);
            $table->json('data')->nullable();
            $table->json('data_code')->nullable();
            $table->text('html_pinya')->nullable();
            $table->text('html_folre')->nullable();
            $table->text('html_manilles')->nullable();
            $table->text('html_puntals')->nullable();
            $table->timestamps();

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
        Schema::dropIfExists('boards');
    }
}
