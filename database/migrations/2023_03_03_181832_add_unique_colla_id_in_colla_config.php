<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueCollaidInCollaConfig extends Migration
{
    public function up()
    {
        Schema::table('colla_config', function (Blueprint $table) {
            $table->unique('colla_id');
        });
    }

    public function down()
    {
        Schema::table('colla_config', function (Blueprint $table) {
            $table->dropForeign(['colla_id']);
            $table->dropUnique(['colla_id']);
            $table->foreign('colla_id')
            ->references('id_colla')->on('colles')
            ->onDelete('cascade');
        });
    }
}
