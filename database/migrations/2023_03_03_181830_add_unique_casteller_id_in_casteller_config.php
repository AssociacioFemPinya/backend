<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueCastelleridInCastellerConfig extends Migration
{
    public function up()
    {
        Schema::table('casteller_config', function (Blueprint $table) {
            $table->unique('casteller_id');
        });
    }

    public function down()
    {
        Schema::table('casteller_config', function (Blueprint $table) {
            $table->dropForeign(['casteller_id']);
            $table->dropUnique(['casteller_id']);
            $table->foreign('casteller_id')
            ->references('id_casteller')->on('castellers')
            ->onDelete('cascade');
        });
    }
}
