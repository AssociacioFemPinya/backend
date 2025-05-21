<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGoogleCalendarTelegram extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('colla_config', function (Blueprint $table) {
            $table->boolean('google_calendar_activitats')->default(0)->after('max_assaigs');
            $table->boolean('google_calendar_actuacions')->default(0)->after('google_calendar_activitats');
            $table->boolean('google_calendar_assaigs')->default(0)->after('google_calendar_actuacions');
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
            $table->dropColumn('google_calendar_activitats');
            $table->dropColumn('google_calendar_actuacions');
            $table->dropColumn('google_calendar_assaigs');
        });
    }
}
