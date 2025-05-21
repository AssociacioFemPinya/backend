<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotpTokenExpiration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('colla_config', function (Blueprint $table) {
            $table->integer('totp_token_expiration')->default(30)->after('language');
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
			$table->dropColumn('totp_token_expiration');
        });
    }
}
