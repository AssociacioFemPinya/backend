<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoardTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('board_tags', function (Blueprint $table) {
            $table->integer('tag_id')->unsigned();
            $table->integer('board_id')->unsigned();

            $table->foreign('tag_id')
                ->references('id_tag')->on('tags')
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
        Schema::dropIfExists('board_tags');
    }
}
