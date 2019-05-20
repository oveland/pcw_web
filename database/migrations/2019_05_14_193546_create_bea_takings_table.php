<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBeaTakingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bea_takings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('date');
            $table->unsignedBigInteger('liquidation_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            /* table relations */
            $table->foreign('liquidation_id')->references('id')->on('bea_liquidations')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bea_takings');
    }
}
