<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id_event');
            $table->integer('id_event_external')->unsigned()->nullable();
            $table->integer('colla_id')->unsigned();
            $table->string('name', 110);
            $table->string('address')->nullable();
            $table->text('comments')->nullable();
            $table->integer('duration')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('open_date')->nullable();
            $table->dateTime('close_date')->nullable();
            $table->tinyInteger('companions')->nullable();
            $table->tinyInteger('visibility')->default(1);
            $table->tinyInteger('type');
            $table->string('photo', 100)->nullable();
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
        Schema::dropIfExists('events');
    }
}
