<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastCredentialsSent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('casteller_config', function (Blueprint $table) {
            $table->dateTime('last_credentials_sent_at')->nullable();
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
            $table->dropColumn('last_credentials_sent_at');
        });
    }
}
