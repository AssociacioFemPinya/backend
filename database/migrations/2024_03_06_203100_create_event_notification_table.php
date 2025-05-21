<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_notification', function (Blueprint $table) {
            $table->id();
            $table->integer('event_id')->unsigned();
            $table->bigInteger('notification_id')->unsigned();
            $table->timestamps();
            $table->foreign('event_id')
            ->references('id_event')->on('events')
            ->onDelete('cascade');
            $table->foreign('notification_id')
            ->references('id_notification')->on('notifications')
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
        Schema::dropIfExists('event_notification');
    }
}
