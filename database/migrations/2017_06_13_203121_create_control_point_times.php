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
            $table->time('time');
            $table->time('time_from_dispatch');
            $table->time('time_next_point');
            $table->integer('day_type_id')->unsigned();
            $table->bigInteger('control_point_id')->unsigned();
            $table->bigInteger('fringe_id')->unsigned()->nullable(true);
            $table->timestamps();
            /* table relations */
            $table->foreign('day_type_id')->references('id')->on('day_types');
            $table->foreign('control_point_id')->references('id')->on('control_points')->onDelete('cascade');
            $table->foreign('fringe_id')->references('id')->on('fringes')->onDelete('cascade');

            /*Indexes*/
            $table->unique(['control_point_id', 'day_type_id', 'fringe_id']); // A control point has a unique time for a day type in a specific fringe
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
