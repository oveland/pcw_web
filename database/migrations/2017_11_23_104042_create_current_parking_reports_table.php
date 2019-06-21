<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrentParkingReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('current_parking_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('date');

            /* Location report */
            $table->bigInteger('location_id')->unsigned()->nullable();
            /* --- */
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->double('orientation')->nullable();
            $table->double('speed')->nullable();
            $table->double('odometer')->nullable();

            /* Route report */
            $table->bigInteger('report_id')->unsigned()->nullable();
            /* --- */
            $table->bigInteger('dispatch_register_id')->nullable();
            $table->integer('distancem')->nullable();
            $table->integer('distancep')->nullable();
            $table->integer('distanced')->nullable();

            $table->string('timem')->nullable();
            $table->string('timep')->nullable();
            $table->string('timed')->nullable();
            $table->double('status_in_minutes')->nullable();
            $table->double('control_point_id')->nullable();
            $table->double('fringe_id')->nullable();

            $table->bigInteger('vehicle_id')->unsigned();

            $table->timestamps();

            /* Table relations */
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            /*
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
            */
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('current_parking_reports');
    }
}
