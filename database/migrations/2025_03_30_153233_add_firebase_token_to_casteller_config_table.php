<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFirebaseTokenToCastellerConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('casteller_config', function (Blueprint $table) {
            $table->string('firebase_token')->nullable()->after('tecnica');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('casteller_config', function (Blueprint $table) {
            $table->dropColumn('firebase_token');
        });
    }
}
