<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehicleBinnaclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_binnacles', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('date')->nullable()->useCurrent();
            $table->integer('type_id');
            $table->bigInteger('vehicle_id');
            $table->bigInteger('user_id');
            $table->string('observations', 256)->nullable();

            $table->timestamps();

            /* table relations */
            $table->foreign('type_id')->references('id')->on('vehicle_binnacle_types')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            /*Indexes*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_binnacles');
    }
}
