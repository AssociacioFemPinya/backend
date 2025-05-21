<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueNotificationdateCollaidTitleInNotifications extends Migration
{
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->unique(['notification_date', 'colla_id', 'title'], 'notificationdate_collaid_title_unique');
        });
    }

    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['colla_id']);
            $table->dropUnique('notificationdate_collaid_title_unique');
            $table->foreign('colla_id')
            ->references('id_colla')->on('colles')
            ->onDelete('cascade');
        });
    }
}
