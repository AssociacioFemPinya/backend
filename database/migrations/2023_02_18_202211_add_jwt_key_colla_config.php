<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJwtKeyCollaConfig extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE `colla_config` MODIFY COLUMN `boards_enabled` TINYINT(1)  NOT NULL  DEFAULT 1 AFTER `translation_assaig`;");

        Schema::table('colla_config', function (Blueprint $table) {
            $table->string('aes256_key_public', 24)->nullable()->after('boards_enabled');
        });
    }

    public function down()
    {
        DB::statement("ALTER TABLE `colla_config` MODIFY COLUMN `boards_enabled` TINYINT(1)  NOT NULL  DEFAULT 1 AFTER `updated_at`;");

        Schema::table('colla_config', function (Blueprint $table) {
            $table->dropColumn('aes256_key_public');
        });
    }
}
