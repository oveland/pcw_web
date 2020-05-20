<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppBattery extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_battery', function (Blueprint $table) {
            $table->id();
            $table->integer('level');
            $table->boolean('charging');
            $table->timestamp('date');
            $table->timestamp('date_changed');
            $table->integer('vehicle_id');

            $table->timestamps();

            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
        });

        Schema::create('app_current_battery', function (Blueprint $table) {
            $table->id();
            $table->integer('level');
            $table->boolean('charging');
            $table->timestamp('date');
            $table->timestamp('date_changed');
            $table->integer('vehicle_id');

            $table->timestamps();

            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_current_battery');
        Schema::dropIfExists('app_battery');
    }
}
