<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveColumnsFromNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign('notifications_user_id_foreign');
            $table->dropUnique('notificationdate_collaid_title_unique');
            $table->dropColumn('notification_date');
            $table->dropColumn('filter_search_type');
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
            $table->dateTime('notification_date')->nullable()->after('user_id');
            $table->string('filter_search_type')->nullable()->default(null)->after('type');

            $table->unique(['notification_date', 'colla_id', 'title'], 'notificationdate_collaid_title_unique');
        });
    }
}
