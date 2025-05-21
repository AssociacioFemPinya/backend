<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_tag', function (Blueprint $table) {
            $table->bigInteger('notification_id')->unsigned();
            $table->integer('tag_id')->unsigned();

            $table->foreign('notification_id')
                ->references('id_notification')->on('notifications')
                ->onDelete('cascade');

            $table->foreign('tag_id')
                ->references('id_tag')->on('tags')
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
        Schema::dropIfExists('castellers_tags');
    }
}
