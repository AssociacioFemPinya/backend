<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCastellerIdNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->integer('casteller_id')->unsigned()->nullable()->default(null)->after('user_id');

            $table->foreign('casteller_id')
            ->references('id_casteller')->on('castellers')
            ->onDelete('cascade');

            $table->dropForeign('notifications_casteller_id_foreign');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['casteller_id']);
            $table->dropColumn('casteller_id');
        });
    }
}
