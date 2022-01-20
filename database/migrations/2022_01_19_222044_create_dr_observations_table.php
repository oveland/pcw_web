<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrObservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dr_observations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('field');
            $table->string('value')->nullable();
            $table->string('old_value')->nullable();
            $table->string('observation');
            $table->unsignedBigInteger('dispatch_register_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('dispatch_register_id')->references('id_registro')->on('registrodespacho')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('dr_observations');
    }
}
