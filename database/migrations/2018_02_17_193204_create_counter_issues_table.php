<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCounterIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('counter_issues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('date');
            $table->integer('total');
            $table->integer('total_prev');
            $table->bigInteger('vehicle_id')->unsigned();
            $table->bigInteger('dispatch_register_id')->unsigned()->nullable();
            $table->double('latitude');
            $table->double('longitude');
            $table->string('frame',255);
            $table->string('items_issues',10000)->nullable();
            $table->string('raspberry_cameras_issues',255)->nullable();
            $table->string('raspberry_check_counter_issue',255)->nullable();
            $table->timestamps();

            /* table relations */
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            //$table->foreign('dispatch_register_id')->references('id')->on('dispatch_registers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('counter_issues');
    }
}
