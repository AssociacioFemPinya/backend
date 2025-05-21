<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCastellerRelationshipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('casteller_relationship', function (Blueprint $table) {
            $table->integer('id_casteller')->unsigned();
            $table->integer('casteller_id')->unsigned();

            $table->foreign('id_casteller')
                ->references('id_casteller')->on('castellers')
                ->onDelete('cascade');

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
        Schema::dropIfExists('casteller_casteller');
    }
}
