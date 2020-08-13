<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRouteTakingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route_takings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('total_production')->nullable();
            $table->integer('control')->nullable();
            $table->double('fuel')->nullable();
            $table->integer('others')->nullable();
            $table->integer('net_production')->nullable();
            $table->text('observations')->nullable();

            $table->unsignedBigInteger('dispatch_register_id')->unique();
            $table->timestamps();

            $table->index('dispatch_register_id'); // TODO replace with foreign on registrodespacho table migration
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('route_takings');
    }
}
