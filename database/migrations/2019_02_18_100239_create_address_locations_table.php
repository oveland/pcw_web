<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('address_locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('location_id')->unsigned();
            $table->string('address');
            $table->integer('status')->default(0);
            $table->timestamps();

            /* table relations */
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');

            /*Indexes*/
            $table->unique(['location_id']); // One vehicle locations has a unique address
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('address_locations');
    }
}
