<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCastellerWebTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('casteller_web', function (Blueprint $table) {
            $table->increments('id_casteller_web');
            $table->integer('casteller_id')->unsigned();
            $table->integer('colla_id')->unsigned();
            $table->string('web_token',24)->nullable();
            $table->integer('casteller_active')->nullable();
            $table->boolean('enabled')->default(0);
            $table->timestamps();

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
        Schema::dropIfExists('casteller_web');
    }
}
