<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFavouriteBoardEventTable extends Migration
{
    public function up()
    {
        Schema::table('board_event', function (Blueprint $table) {
            $table->boolean('favourite')->default(false)->after('display');
        });
    }

    public function down()
    {
        Schema::table('board_event', function (Blueprint $table) {
            $table->dropColumn('favourite');
        });
    }
}
