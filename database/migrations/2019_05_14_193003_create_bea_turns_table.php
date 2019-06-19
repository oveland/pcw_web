<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBeaTurnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bea_turns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('route_id');
            $table->unsignedInteger('driver_id')->nullable(true);
            $table->timestamps();

            /* table relations */
            $table->foreign('vehicle_id')->references('bea_id')->on('vehicles')->onDelete('cascade');
            $table->foreign('route_id')->references('bea_id')->on('routes')->onDelete('cascade');
            //$table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bea_turns');
    }
}
