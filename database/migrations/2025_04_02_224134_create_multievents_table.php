<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMultieventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('multievents', function (Blueprint $table) {
            $table->increments('id_multievent');
            $table->integer('colla_id')->unsigned();
            $table->string('name', 110);
            $table->string('address')->nullable();
            $table->string('location_link')->nullable();
            $table->text('comments')->nullable();
            $table->integer('duration')->nullable();
            $table->time('time')->nullable();
            $table->tinyInteger('companions')->nullable();
            $table->tinyInteger('visibility')->default(1);
            $table->tinyInteger('type');
            $table->string('photo', 100)->nullable();
            $table->timestamps();

            $table->foreign('colla_id')
                ->references('id_colla')->on('colles')
                ->onDelete('cascade');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->integer('id_multievent')->unsigned()->nullable();
            $table->foreign('id_multievent')
                ->references('id_multievent')->on('multievents')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['id_multievent']);
            $table->dropColumn('id_multievent');
        });

        Schema::dropIfExists('multievents');
    }
}
