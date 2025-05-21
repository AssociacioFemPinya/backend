<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisplayBoardEvent extends Migration
{
    public function up()
    {
        Schema::table('board_event', function (Blueprint $table) {
            $table->boolean('display')->default(false)->after('board_id');
        });
    }

    public function down()
    {
        Schema::table('board_event', function (Blueprint $table) {
            $table->dropColumn('display');
        });
    }
}
