<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_order', function (Blueprint $table) {
            $table->increments('id_notification_order');
            $table->bigInteger('notification_id')->unsigned();
            $table->integer('casteller_id')->unsigned();
            $table->timestamps();
            $table->foreign('notification_id')
                ->references('id_notification')->on('notifications')
                ->onDelete('cascade');

            $table->foreign('casteller_id')
                ->references('id_casteller')->on('castellers')
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
        Schema::dropIfExists('notification_order');
    }
}
