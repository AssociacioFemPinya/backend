<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id_tag');
            $table->integer('id_tag_external')->unsigned()->nullable();
            $table->integer('colla_id')->unsigned();
            $table->string('name');
            $table->string('value');
            $table->string('group')->nullable();
            $table->enum('type', ['CASTELLERS', 'EVENTS', 'ATTENDANCE', 'BOARDS', 'POSITIONS']);
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
        Schema::dropIfExists('tags');
    }
}
