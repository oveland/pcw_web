<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateControlPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('control_points', function (Blueprint $table) {
            $table->increments('id');
            $table->string('latitude');
            $table->string('longitude');
            $table->string('name');
            $table->integer('order');
            $table->integer('trajectory');
            $table->string('type');
            $table->integer('distance_from_dispatch');
            $table->integer('distance_next_point');
            $table->bigInteger('route_id')->unsigned();
            $table->timestamps();

            /* table relations */
            $table->foreign('route_id')->references('id')->on('routes');

            /*Indexes*/
            $table->index(['name', 'route_id']); // One route has a unique control point name
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('control_points');
    }
}
