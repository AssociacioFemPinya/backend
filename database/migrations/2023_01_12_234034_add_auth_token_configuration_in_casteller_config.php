<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddAuthTokenConfigurationInCastellerConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('casteller_config', function ($table) {
            $table->boolean('auth_token_enabled')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('casteller_config', function ($table) {
            $table->dropColumn('auth_token_enabled');
        });
    }
}
