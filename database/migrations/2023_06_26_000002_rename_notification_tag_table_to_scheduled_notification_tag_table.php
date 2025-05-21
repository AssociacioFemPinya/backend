<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameNotificationTagTableToScheduledNotificationTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('notification_tag', 'scheduled_notification_tag');
        Schema::table('scheduled_notification_tag', function (Blueprint $table) {
          $table->renameColumn('notification_id', 'scheduled_notification_id');
          $table->dropForeign('notification_tag_notification_id_foreign');
          $table->foreign('scheduled_notification_id')
                ->references('id_scheduled_notification')->on('scheduled_notifications')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('scheduled_notification_tag', 'notification_tag');
        Schema::table('notification_tag', function (Blueprint $table) {
          $table->renameColumn('scheduled_notification_id', 'notification_id');
          $table->dropForeign('scheduled_notification_tag_scheduled_notification_id_foreign');
          $table->foreign('notification_id')
                ->references('id_notification')->on('notifications')
                ->onDelete('cascade');
      });
    }
}
