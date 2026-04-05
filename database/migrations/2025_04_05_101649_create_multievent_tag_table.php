<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMultieventTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('multievent_tag', function (Blueprint $table) {
            $table->integer('multievent_id')->unsigned();
            $table->integer('tag_id')->unsigned();

            $table->foreign('multievent_id')
                ->references('id_multievent')->on('multievents')
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
        Schema::dropIfExists('multievent_tag');
    }
}
