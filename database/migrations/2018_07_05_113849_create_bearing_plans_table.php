<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBearingPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bearing_plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date')->default(Carbon::now());
            $table->bigInteger('vehicle_id')->unsigned()->nullable();
            $table->bigInteger('route_id');
            $table->bigInteger('dispatch_register_id')->nullable();
            $table->integer('day_type_id');
            $table->integer('turn');
            $table->integer('round_trip');
            $table->time('departure_time_scheduled');
            $table->time('arrival_time_scheduled');
            $table->time('route_time');
            $table->integer('initial_point')->default(1);
            $table->string('control_point_times',1000);
            $table->boolean('active')->default(false);

            $table->timestamps();

            /* Table relations */
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('route_id')->references('id')->on('routes')->onDelete('cascade');
            $table->foreign('day_type_id')->references('id')->on('day_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bearing_plans');
    }
}
