<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduledNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheduled_notifications', function (Blueprint $table) {
            $table->bigIncrements('id_scheduled_notification');
            $table->integer('colla_id')->unsigned();
            $table->bigInteger('notification_id')->unsigned()->nullable();
            $table->string('title');
            $table->text('body');
            $table->integer('user_id')->unsigned();
            $table->dateTime('notification_date')->nullable();
            $table->tinyInteger('visible')->nullable();
            $table->integer('type');
            $table->string('filter_search_type')->nullable()->default(null);
            $table->timestamps();

            $table->unique(['notification_date', 'colla_id', 'title'], 'notificationdate_collaid_title_unique');

            $table->foreign('notification_id')
                ->references('id_notification')->on('notifications')
                ->onDelete('cascade');

            $table->foreign('colla_id')
                ->references('id_colla')->on('colles')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id_user')->on('users')
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
        Schema::dropIfExists('scheduled_notifications');
    }
}
