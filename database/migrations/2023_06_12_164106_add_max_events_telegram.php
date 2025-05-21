<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaxEventsTelegram extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('colla_config', function (Blueprint $table) {
            $table->integer('max_activitats')->default(6);
            $table->integer('max_actuacions')->default(6);
            $table->integer('max_assaigs')->default(6);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('colla_config', function (Blueprint $table) {
            $table->dropColumn('max_activitats');
            $table->dropColumn('max_actuacions');
            $table->dropColumn('max_assaigs');
        });
    }
}
