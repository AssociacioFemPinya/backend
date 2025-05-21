<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKeysRowsTable extends Migration
{
    public function up()
    {
        Schema::table('rows', function (Blueprint $table) {
           // $table->unique(['board_id', 'div_id', 'base'], 'div_on_board');
        });
    }

    public function down()
    {
        Schema::table('rows', function (Blueprint $table) {
            //            $table->dropUnique('div_on_board');
        });
    }
}
