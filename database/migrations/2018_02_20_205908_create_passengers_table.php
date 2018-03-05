<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePassengersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('passengers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('date');
            $table->integer('total');
            $table->integer('total_prev');
            $table->bigInteger('vehicle_id')->unsigned();
            $table->bigInteger('dispatch_register_id')->unsigned()->nullable();
            $table->bigInteger('location_id')->unsigned()->nullable();
            $table->bigInteger('counter_issue_id')->unsigned()->nullable();
            $table->double('latitude');
            $table->double('longitude');
            $table->string('frame',255);
            $table->timestamps();

            /* table relations */
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('counter_issue_id')->references('id')->on('counter_issues')->onDelete('cascade');
            //$table->foreign('dispatch_register_id')->references('id')->on('dispatch_registers')->onDelete('cascade');
            //$table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('passengers');
    }
}
