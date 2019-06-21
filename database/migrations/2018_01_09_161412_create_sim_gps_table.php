<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSimGpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sim_gps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sim')->unique();
            $table->string('operator');
            $table->string('gps_type');
            $table->bigInteger('vehicle_id')->unique();
            $table->boolean('active')->default(true);
            $table->timestamps();

            /* Table relations */
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sim_gps');
    }
}
