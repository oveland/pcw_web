<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateControlPointsFicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('control_points_fics', function (Blueprint $table) {
            $table->increments('id');

            $table->string('fics_id', 32);
            $table->unsignedBigInteger('control_point_id');

            $table->foreign('control_point_id')->references('id')->on('control_points')->onDelete('cascade');

            $table->unique(['control_point_id']);
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
        Schema::dropIfExists('control_points_fics');
    }
}
