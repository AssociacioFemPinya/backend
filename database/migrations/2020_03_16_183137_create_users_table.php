<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id_user');
            $table->integer('colla_id')->unsigned();
            $table->string('role', 10);
            $table->integer('type')->unsigned();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('language', 2)->default('ca');
            $table->string('photo', 100)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->dateTime('last_access_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
