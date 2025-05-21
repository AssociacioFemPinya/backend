<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueCollaidNameValueTypeInTags extends Migration
{
    public function up()
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->unique(['colla_id', 'name', 'value', 'type'], 'collaid_name_value_type_unique');
        });
    }

    public function down()
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropForeign(['colla_id']);
            $table->dropUnique('collaid_name_value_type_unique');
            $table->foreign('colla_id')
            ->references('id_colla')->on('colles')
            ->onDelete('cascade');
        });
    }
}
