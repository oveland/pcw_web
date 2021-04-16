<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearRouteDispatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route_dispatches', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('route_id');
            $table->integer('origin_dispatch_id');
            $table->integer('destination_dispatch_id');

            $table->foreign('route_id')->references('id')->on('routes')->onDelete('cascade');
            $table->foreign('origin_dispatch_id')->references('id')->on('dispatches')->onDelete('cascade');
            $table->foreign('destination_dispatch_id')->references('id')->on('dispatches')->onDelete('cascade');

            $table->unique(['route_id', 'origin_dispatch_id', 'destination_dispatch_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('route_dispatches');
    }
}
