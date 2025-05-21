<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollaConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('colla_config', function (Blueprint $table) {
            $table->increments('id_colla_config');
            $table->integer('colla_id')->unsigned();
            $table->string('translation_activitat', 20)->nullable();
            $table->string('translation_actuacio', 20)->nullable();
            $table->string('translation_assaig', 20)->nullable();

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
        Schema::dropIfExists('colla_config');
    }
}
