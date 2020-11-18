<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehicleBinnacleNotificationsUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_binnacle_notifications_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('notification_id');
            $table->boolean('email_notified')->default(false);
            $table->timestamp('email_notified_at')->nullable();
            $table->boolean('platform_notified')->default(false);
            $table->timestamp('platform_notified_at')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('notification_id')->references('id')->on('vehicle_binnacle_notifications')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_binnacle_notifications_users');
    }
}
