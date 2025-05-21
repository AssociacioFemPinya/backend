<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueNameLastnameAliasInCastellers extends Migration
{
    public function up()
    {
        Schema::table('castellers', function (Blueprint $table) {
            $table->unique(['name', 'last_name', 'alias'], 'name_surname_alias_unique');
        });
    }

    public function down()
    {
        Schema::table('castellers', function (Blueprint $table) {
            $table->dropUnique('name_surname_alias_unique');
        });
    }
}
