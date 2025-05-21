<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCastellerTelegramTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('casteller_telegram', function (Blueprint $table) {
            $table->increments('id_casteller_telegram');
            $table->integer('casteller_id')->unsigned();
            $table->integer('colla_id')->unsigned();
            $table->string('telegram_id', 13);
            $table->integer('casteller_active_id')->unsigned();
            $table->timestamps();

            $table->foreign('casteller_id')
                ->references('id_casteller')->on('castellers')
                ->onDelete('cascade');

            $table->foreign('colla_id')
                ->references('id_colla')->on('colles')
                ->onDelete('cascade');

            $table->foreign('casteller_active_id')
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
        Schema::dropIfExists('casteller_telegram');
    }
}
