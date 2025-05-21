<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPublicDisplayEnabledCollaConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('colla_config', function (Blueprint $table) {
            $table->boolean('public_display_enabled')->default(0)->after('member_session_expire');
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
            $table->dropColumn('public_display_enabled');
        });
    }
}
