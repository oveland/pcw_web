<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBeaMarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bea_marks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('turn_id');
            $table->unsignedInteger('trajectory_id')->nullable();
            $table->timestamp('date')->useCurrent();
            $table->time('initial_time');
            $table->time('final_time');
            $table->integer('passengers_up');
            $table->integer('passengers_down');
            $table->integer('locks');
            $table->integer('auxiliaries');
            $table->integer('boarded');
            $table->double('im_bea_max');
            $table->double('im_bea_min');
            $table->integer('total_bea');
            $table->integer('passengers_bea');

            $table->boolean('liquidated')->default(false);
            $table->unsignedBigInteger('liquidation_id')->nullable();
            $table->boolean('taken')->default(false);
            $table->integer('pay_fall')->nullable();
            $table->integer('get_fall')->nullable();

            $table->timestamps();

            /* Table relations */
            $table->foreign('turn_id')->references('id')->on('bea_turns')->onDelete('cascade');
            $table->foreign('trajectory_id')->references('id')->on('bea_trajectories')->onDelete('cascade');
            $table->foreign('liquidation_id')->references('id')->on('bea_liquidations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bea_marks');
    }
}
