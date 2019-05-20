<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBeaLiquidationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bea_liquidations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('date');
            $table->unsignedBigInteger('vehicle_id');
            $table->text('liquidation');
            $table->text('totals');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            /* table relations */
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
        Schema::dropIfExists('bea_liquidations');
    }
}
