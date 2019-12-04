<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBeaMarkDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // table bea_mark_discounts save/hold data discounts in process liquidation
        Schema::create('bea_mark_discounts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('discount_type_id');
            $table->unsignedBigInteger('route_id');
            $table->unsignedInteger('trajectory_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->integer('value')->default(0);
            $table->unsignedBigInteger('mark_id');
            $table->timestamps();

            /* Table relations */
            $table->foreign('discount_type_id')->references('id')->on('bea_mark_discount_types')->onDelete('cascade');
            $table->foreign('route_id')->references('id')->on('routes')->onDelete('cascade');
            $table->foreign('trajectory_id')->references('id')->on('bea_trajectories')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('mark_id')->references('id')->on('bea_marks')->onDelete('cascade');

            /*Indexes*/
            $table->unique(['discount_type_id', 'route_id', 'trajectory_id', 'vehicle_id', 'mark_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bea_mark_discounts');
    }
}
