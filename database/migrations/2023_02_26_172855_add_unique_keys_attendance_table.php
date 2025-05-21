<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueKeysAttendanceTable extends Migration
{
    public function up()
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->unique(['id_attendance', 'event_id', 'casteller_id']);
        });
    }

    public function down()
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropUnique('attendance_id_attendance_event_id_casteller_id_unique');
        });
    }
}
