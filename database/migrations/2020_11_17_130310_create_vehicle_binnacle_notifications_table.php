<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehicleBinnacleNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_binnacle_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('binnacle_id');
            $table->timestamp('date')->nullable();
            $table->integer('period')->default(0)->nullable();
            $table->integer('day_of_month')->default(0)->nullable();
            $table->integer('day_of_week')->default(0)->nullable();

            $table->timestamps();

            $table->foreign('binnacle_id')->references('id')->on('vehicle_binnacles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_binnacle_notifications');
    }
}
