<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProprietariesVehiclesView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proprietary_vehicles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('vehicle_id')->unsigned();
            $table->bigInteger('proprietary_id')->unsigned();
            $table->boolean('active')->default(true);
            $table->timestamps();

            /* Table relations */
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            //$table->foreign('proprietary_id')->references('id')->on('proprietaries')->onDelete('cascade'); // TODO uncomment when proprietaries view is migrate to a table

            /*Indexes*/
            $table->unique(['proprietary_id', 'vehicle_id']); // One plate has a unique vehicle number
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('proprietary_vehicles');
    }
}
