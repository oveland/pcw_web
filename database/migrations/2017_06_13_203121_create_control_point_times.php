<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateControlPointTimes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('control_point_times', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('fringe_1');
            $table->string('total_time');
            $table->bigInteger('route_id')->unsigned();
            $table->bigInteger('day_type_id')->unsigned();
            $table->bigInteger('control_point_id')->unsigned();

            /* table relations */
            $table->foreign('route_id')->references('id')->on('routes');
            $table->foreign('day_type_id')->references('id')->on('day_types');
            $table->foreign('control_point_id')->references('id')->on('control_points');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('control_point_times');
    }
}
