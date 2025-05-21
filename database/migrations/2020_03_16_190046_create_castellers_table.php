<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCastellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('castellers', function (Blueprint $table) {
            $table->increments('id_casteller');
            $table->integer('id_casteller_external')->unsigned()->nullable();
            $table->integer('colla_id')->unsigned();
            $table->integer('num_soci')->nullable();
            $table->string('national_id_number', 50)->nullable();
            $table->string('national_id_type', 50)->nullable();
            $table->string('name', 150)->nullable();
            $table->string('last_name', 150)->nullable();
            $table->string('family', 150)->nullable();
            $table->tinyInteger('family_head')->nullable();
            $table->string('alias', 150);
            $table->tinyInteger('gender')->nullable();
            $table->date('birthdate')->nullable();
            $table->date('subscription_date')->nullable();
            $table->string('email')->nullable();
            $table->string('email2')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('mobile_phone', 20)->nullable();
            $table->string('emergency_phone', 20)->nullable();
            $table->string('address')->nullable();
            $table->string('postal_code', 5)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('comarca', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->text('comments')->nullable();
            $table->string('photo', 100)->nullable();
            $table->float('height')->nullable();
            $table->float('weight')->nullable();
            $table->float('shoulder_height')->nullable();
            $table->tinyInteger('status');
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
        Schema::dropIfExists('castellers');
    }
}
