<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehicleSeatDistributionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_seat_distributions', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('vehicle_id');
            $table->integer('vehicle_seat_topology_id');
            $table->string('json_distribution');
            $table->timestamps();

            /* Table relations */
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('vehicle_seat_topology_id')->references('id')->on('vehicle_seat_topologies')->onDelete('cascade');

            /*Indexes*/
            $table->unique('vehicle_id'); // One vehicle has an unique seat distribution
            $table->unique(['vehicle_id', 'vehicle_seat_topology_id']); // One vehicle has an unique seat topology
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_seat_distributions');
    }
}
