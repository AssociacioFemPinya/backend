<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueEventidCastelleridInAssistance extends Migration
{
    public function up()
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->unique(['event_id', 'casteller_id'], 'casteller_event_unique');
        });
    }

    public function down()
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropForeign(['casteller_id']);
            $table->dropUnique('casteller_event_unique');
            $table->foreign('event_id')
                ->references('id_event')->on('events')
                ->onDelete('cascade');
            $table->foreign('casteller_id')
                ->references('id_casteller')->on('castellers')
                ->onDelete('cascade');
        });
    }
}
