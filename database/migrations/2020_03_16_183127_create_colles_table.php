<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('colles', function (Blueprint $table) {
            $table->increments('id_colla');
            $table->integer('id_colla_external')->unsigned()->nullable();
            $table->string('name', 50);
            $table->string('shortname', 20)->unique();
            $table->string('email')->nullable();
            $table->string('phone', 12)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('logo', 100)->nullable();
            $table->string('banner', 100)->nullable();
            $table->string('color', 7)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('colles');
    }
}
