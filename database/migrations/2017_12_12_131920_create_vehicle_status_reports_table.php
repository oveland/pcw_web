<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehicleStatusReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_status_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->time('time');
            $table->bigInteger('vehicle_id')->nullable();
            $table->bigInteger('dispatch_register_id')->nullable();
            $table->integer('vehicle_status_id')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->double('orientation')->nullable();
            $table->double('speed')->nullable();
            $table->bigInteger('odometer')->nullable();
            $table->string('frame')->nullable();
            $table->timestamps();

            /* Table relations */
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            //$table->foreign('dispatch_register_id')->references('id')->on('dispatch_registers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_status_reports');
    }
}
