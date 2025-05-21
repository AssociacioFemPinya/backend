<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCastellerConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('casteller_config', function (Blueprint $table) {
            $table->increments('id_casteller_config');
            $table->integer('casteller_id')->unsigned();
            $table->string('telegram_token',8)->nullable();
            $table->boolean('telegram_enabled')->default(1);
            $table->boolean('tecnica')->default(0);
            $table->dateTime('last_access_at')->nullable();
            $table->timestamps();

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
        Schema::dropIfExists('casteller_config');
    }
}
