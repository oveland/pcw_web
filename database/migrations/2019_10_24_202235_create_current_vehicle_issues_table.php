<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrentVehicleIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('current_vehicle_issues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('date')->useCurrent();
            $table->integer('issue_type_id');
            $table->string('issue_uid')->unique();
            $table->bigInteger('vehicle_id');
            $table->bigInteger('dispatch_register_id')->nullable();
            $table->bigInteger('driver_id')->nullable();
            $table->bigInteger('user_id');
            $table->string('observations', 256)->nullable();

            $table->timestamps();

            /* table relations */
            $table->foreign('issue_type_id')->references('id')->on('vehicle_issue_types')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('dispatch_register_id')->references('id_registro')->on('registrodespacho')->onDelete('cascade');
            //$table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');
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
        Schema::dropIfExists('current_vehicle_issues');
    }
}
