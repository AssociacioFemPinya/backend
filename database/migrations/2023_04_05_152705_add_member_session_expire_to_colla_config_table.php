<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMemberSessionExpireToCollaConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('colla_config', function (Blueprint $table) {
            $table->boolean('member_session_expire')->default(1)->after('boards_enabled');
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
            $table->dropColumn('member_session_expire');
        });
    }
}
