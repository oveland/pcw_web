<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeaAdvancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bea_advances', function (Blueprint $table) {
            $table->id();
            $table->integer('value')->default(0);
            $table->unsignedBigInteger('vehicle_id');
            $table->boolean('liquidated')->default(false);
            $table->unsignedBigInteger('liquidation_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->timestamps();

            $table->foreign('vehicle_id')->references('id')->on('vehicles');
            $table->foreign('liquidation_id')->references('id')->on('bea_liquidations');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bea_advances');
    }
}
