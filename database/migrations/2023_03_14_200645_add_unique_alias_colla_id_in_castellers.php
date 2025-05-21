<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueAliasCollaIdInCastellers extends Migration
{
    public function up()
    {
        Schema::table('castellers', function (Blueprint $table) {
            # This is required to remove the unique only if exists (otherwise the migration would fail if it doesn't exist)
            $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes($table->getTable());
            $index_name = 'name_surname_alias_unique';

            if (array_key_exists($index_name, $indexes)) {
                $table->dropUnique('name_surname_alias_unique');
            }
        });
        Schema::table('castellers', function (Blueprint $table) {
            $table->unique(['alias', 'colla_id'], 'alias_collaid_unique');
        });
    }

    public function down()
    {
        Schema::table('castellers', function (Blueprint $table) {
            $table->dropUnique('alias_collaid_unique');
        });
    }
}
