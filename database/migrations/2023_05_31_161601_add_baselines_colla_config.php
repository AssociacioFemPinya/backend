<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBaselinesCollaConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('colla_config', function (Blueprint $table) {
			$table->integer('height_baseline')->default(0)->after('aes256_key_public');
			$table->integer('shoulder_height_baseline')->default(0)->after('height_baseline');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('colla_config', function (Blueprint $table) {
			$table->dropColumn('height_baseline');
            $table->dropColumn('shoulder_height_baseline');
        });
    }
}
