<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateControlPointsTariffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('control_points_tariffs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('from_control_point_id');
            $table->unsignedBigInteger('to_control_point_id');
            $table->integer('value')->default(0);

            $table->timestamps();


            $table->foreign('from_control_point_id')->references('id')->on('control_points')->onDelete('cascade');
            $table->foreign('to_control_point_id')->references('id')->on('control_points')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('control_point_tariffs');
    }
}
