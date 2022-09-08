<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehicleMemosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_memos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date')->default(DB::raw('CURRENT_DATE'));
            $table->bigInteger('vehicle_id');
            $table->bigInteger('created_user_id');
            $table->bigInteger('edited_user_id')->nullable();
            $table->text('observations');

            $table->timestamps();

            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('created_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('edited_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_memos');
    }
}
