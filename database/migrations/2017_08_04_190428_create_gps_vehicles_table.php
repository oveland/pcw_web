<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGpsVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gps_vehicles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('vehicle_id')->unique();
            $table->string('imei')->unique();
            $table->timestamps();

            /* table relations */
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');

            /*Indexes*/
            $table->unique(['vehicle_id', 'imei']); // One imei is assigned to unique vehicle
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gps_vehicles');
    }
}
