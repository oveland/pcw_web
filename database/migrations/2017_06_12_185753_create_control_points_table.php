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
            $table->bigIncrements('id');
            $table->double('latitude');
            $table->double('longitude');
            $table->string('name');
            $table->integer('order');
            $table->integer('trajectory');
            $table->string('type');
            $table->integer('distance_from_dispatch');
            $table->integer('distance_next_point');
            $table->bigInteger('route_id')->unsigned();
            $table->timestamps();

            /* Table relations */
            $table->foreign('route_id')->references('id')->on('routes')->onDelete('cascade');

            /*Indexes*/
            $table->unique(['name', 'route_id', 'trajectory']); // One route has a unique control point name
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
