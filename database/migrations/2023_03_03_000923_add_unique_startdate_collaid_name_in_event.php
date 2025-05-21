<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueStartdateCollaidNameInEvent extends Migration
{
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->unique(['start_date', 'colla_id', 'name'], 'startdate_collaid_name_unique');
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['colla_id']);
            $table->dropUnique('startdate_collaid_name_unique');
            $table->foreign('colla_id')
                ->references('id_colla')->on('colles')
                ->onDelete('cascade');
        });
    }
}
